<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Ambil proyek + client + phases (progress phase dipakai)
        $projects = Project::with(['client', 'phases' => function($q){
            $q->orderBy('urutan');
        }])->latest()->get();

        // helper hitung progress total per proyek (bobot tahapan)
        $calcProgressTotal = function($project){
            return $project->phases->sum(function($p){
                $phaseProgress = (int)($p->progress ?? 0);
                return ((int)$p->persen * $phaseProgress) / 100;
            });
        };

        // Klasifikasi status + progressTotal per proyek
        $statusCounts = [
            'Belum Mulai' => 0,
            'Aktif'       => 0,
            'Selesai'     => 0,
            'Terlambat'   => 0,
        ];

        $projectRows = $projects->map(function($p) use ($today, $calcProgressTotal, &$statusCounts){
            $progressTotal = $calcProgressTotal($p);

            $mulai   = Carbon::parse($p->tanggal_mulai);
            $selesai = Carbon::parse($p->tanggal_selesai);

            if ($progressTotal >= 100) {
                $status = 'Selesai';
            } elseif ($today->lt($mulai)) {
                $status = 'Belum Mulai';
            } elseif ($today->gt($selesai)) {
                $status = 'Terlambat';
            } else {
                $status = 'Aktif';
            }

            $statusCounts[$status]++;

            return (object)[
                'id' => $p->id,
                'nama_proyek' => $p->nama_proyek,
                'client' => $p->client->nama ?? '-',
                'tanggal_mulai' => $p->tanggal_mulai,
                'tanggal_selesai' => $p->tanggal_selesai,
                'progress_total' => round($progressTotal, 1),
                'status' => $status,
            ];
        });

        // Cards
        $totalProjects  = $projectRows->count();
        $aktifProjects  = $projectRows->where('status','Aktif')->count();
        $selesaiProjects= $projectRows->where('status','Selesai')->count();
        $telatProjects  = $projectRows->where('status','Terlambat')->count();

        // Chart bar: proyek aktif
        $active = $projectRows->where('status','Aktif')->values();
        $barLabels = $active->pluck('nama_proyek')->toArray();
        $barData   = $active->pluck('progress_total')->toArray();

        // Chart pie: status
        $pieLabels = array_keys($statusCounts);
        $pieData   = array_values($statusCounts);

        // Tabel perhatian: telat atau progress rendah (misal < 30 tapi sudah berjalan)
        $attention = $projectRows->filter(function($r) use ($today){
            $mulai = Carbon::parse($r->tanggal_mulai);
            $selesai = Carbon::parse($r->tanggal_selesai);

            $totalDays = max(1, $mulai->diffInDays($selesai) + 1);
            $passedDays = $mulai->gt($today) ? 0 : min($totalDays, $mulai->diffInDays($today) + 1);
            $timeRatio = ($passedDays / $totalDays) * 100; // % waktu berjalan

            // aturan: telat ATAU (waktu sudah jalan > 40% tapi progress < 30%)
            if ($r->status === 'Terlambat') return true;
            if ($r->status === 'Aktif' && $timeRatio > 40 && $r->progress_total < 30) return true;

            return false;
        })->values()->take(5);

        return view('admin.dashboard', compact(
            'totalProjects','aktifProjects','selesaiProjects','telatProjects',
            'barLabels','barData','pieLabels','pieData',
            'attention'
        ));
    }
}
