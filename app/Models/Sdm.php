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

    public function projects()
    {
        return $this->belongsToMany(\App\Models\Project::class, 'project_sdm')
            ->withTimestamps();
    }
    public function projectSdms()
    {
        return $this->hasMany(\App\Models\ProjectSdm::class);
    }


}
