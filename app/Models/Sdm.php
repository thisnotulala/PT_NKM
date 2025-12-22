<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sdm extends Model
{
    use HasFactory;

    protected $table = 'sdms';

    protected $fillable = [
        'nama',
        'peran',
        'nomor_telepon',
        'alamat',
    ];
}
