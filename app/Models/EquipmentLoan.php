<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentLoan extends Model
{
    protected $fillable = [
        'project_id','status','tanggal_pinjam','tanggal_kembali','catatan',
        'requested_by','approved_by','approved_at','rejected_at','returned_at'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(EquipmentLoanItem::class, 'loan_id');
    }
}
