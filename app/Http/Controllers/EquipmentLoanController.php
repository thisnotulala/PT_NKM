<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentLoan;
use App\Models\EquipmentLoanItem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentLoanController extends Controller
{
    public function index()
    {
        $loans = EquipmentLoan::with('project')
            ->latest()->get();

        return view('equipment_loans.index', compact('loans'));
    }

    public function create()
    {
        $projects = Project::orderBy('nama_proyek')->get();
        $equipment = Equipment::with('satuan')->orderBy('nama_alat')->get();

        return view('equipment_loans.create', compact('projects','equipment'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tanggal_pinjam' => 'required|date',
            'catatan' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.equipment_id' => 'required|exists:equipment,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        // (opsional) pre-check stok supaya user tidak ajukan hal mustahil
        foreach ($data['items'] as $it) {
            $eq = Equipment::find($it['equipment_id']);
            if ($eq->stok < (int)$it['qty']) {
                return back()->withInput()->withErrors([
                    'items' => "Stok {$eq->nama_alat} tidak cukup. Stok: {$eq->stok}, diminta: {$it['qty']}"
                ]);
            }
        }

        DB::transaction(function() use ($data) {
            $loan = EquipmentLoan::create([
                'project_id' => $data['project_id'],
                'status' => 'pending',
                'tanggal_pinjam' => $data['tanggal_pinjam'],
                'catatan' => $data['catatan'] ?? null,
                'requested_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $it) {
                EquipmentLoanItem::create([
                    'loan_id' => $loan->id,
                    'equipment_id' => $it['equipment_id'],
                    'qty' => (int)$it['qty'],
                ]);
            }
        });

        return redirect()->route('equipment_loans.index')
            ->with('success', 'Pengajuan peminjaman dibuat (pending).');
    }

    public function show(EquipmentLoan $loan)
    {
        $loan->load(['project','items.equipment.satuan']);
        return view('equipment_loans.show', compact('loan'));
    }

    public function approve(EquipmentLoan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Hanya peminjaman pending yang bisa di-approve.');
        }

        try {
            DB::transaction(function() use ($loan) {
                $loan->load('items');

                // lock equipment rows untuk aman dari race condition
                foreach ($loan->items as $item) {
                    $eq = Equipment::where('id', $item->equipment_id)->lockForUpdate()->first();
                    if ($eq->stok < $item->qty) {
                        throw new \Exception("Stok {$eq->nama_alat} tidak cukup.");
                    }
                }

                foreach ($loan->items as $item) {
                    $eq = Equipment::where('id', $item->equipment_id)->lockForUpdate()->first();
                    $eq->update(['stok' => $eq->stok - $item->qty]);
                }

                $loan->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Peminjaman disetujui. Stok otomatis berkurang.');
    }

    public function reject(Request $request, EquipmentLoan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Hanya peminjaman pending yang bisa ditolak.');
        }

        $request->validate(['catatan' => 'nullable|string']);

        $loan->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'approved_by' => auth()->id(),
            'catatan' => $request->catatan ?? $loan->catatan,
        ]);

        return back()->with('success', 'Pengajuan ditolak.');
    }

    public function returnForm(EquipmentLoan $loan)
    {
        if ($loan->status !== 'approved') {
            return redirect()->route('equipment_loans.show', $loan->id)
                ->with('error', 'Hanya peminjaman approved yang bisa dikembalikan.');
        }

        $loan->load(['project','items.equipment.satuan']);
        return view('equipment_loans.return', compact('loan'));
    }

    public function returnStore(Request $request, EquipmentLoan $loan)
    {
        if ($loan->status !== 'approved') {
            return back()->with('error', 'Status tidak valid untuk pengembalian.');
        }

        $data = $request->validate([
            'tanggal_kembali' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.qty_baik' => 'required|integer|min:0',
            'items.*.qty_rusak' => 'required|integer|min:0',
            'items.*.qty_hilang' => 'required|integer|min:0',
            'items.*.catatan_kondisi' => 'nullable|string',
        ]);

        try {
            DB::transaction(function() use ($loan, $data) {
                $loan->load('items');

                foreach ($loan->items as $item) {
                    $in = $data['items'][$item->id] ?? null;
                    if (!$in) throw new \Exception("Data item pengembalian tidak lengkap.");

                    $baik = (int)$in['qty_baik'];
                    $rusak = (int)$in['qty_rusak'];
                    $hilang = (int)$in['qty_hilang'];

                    if (($baik + $rusak + $hilang) !== (int)$item->qty) {
                        throw new \Exception("Total kembali untuk {$item->equipment->nama_alat} harus sama dengan qty dipinjam ({$item->qty}).");
                    }

                    // update detail return
                    $item->update([
                        'qty_baik' => $baik,
                        'qty_rusak' => $rusak,
                        'qty_hilang' => $hilang,
                        'catatan_kondisi' => $in['catatan_kondisi'] ?? null,
                    ]);

                    // stok kembali hanya yg baik
                    if ($baik > 0) {
                        $eq = Equipment::where('id', $item->equipment_id)->lockForUpdate()->first();
                        $eq->update(['stok' => $eq->stok + $baik]);
                    }
                }

                $loan->update([
                    'status' => 'returned',
                    'tanggal_kembali' => $data['tanggal_kembali'],
                    'returned_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('equipment_loans.show', $loan->id)
            ->with('success', 'Pengembalian dicatat. Stok bertambah sesuai qty baik.');
    }
}
