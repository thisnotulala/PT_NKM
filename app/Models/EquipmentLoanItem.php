<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentLoanItem extends Model
{
    protected $fillable = [
        'loan_id','equipment_id','qty','qty_baik','qty_rusak','qty_hilang','catatan_kondisi'
    ];

    public function loan()
    {
        return $this->belongsTo(EquipmentLoan::class, 'loan_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
