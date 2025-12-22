<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = [
        'nama_alat',
        'satuan_id',
        'stok',
        'kondisi',
    ];

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }
}
