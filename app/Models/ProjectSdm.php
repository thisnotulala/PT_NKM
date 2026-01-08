<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSdm extends Model
{
    protected $table = 'project_sdms';

    protected $fillable = [
        'project_id',
        'sdm_id',
        'peran_di_proyek',
    ];

    // Project.php
    public function sdms()
    {
        return $this->belongsToMany(\App\Models\Sdm::class, 'project_sdms')
            ->withTimestamps();
    }

    // Sdm.php
    public function projects()
    {
        return $this->belongsToMany(\App\Models\Project::class, 'project_sdms')
            ->withTimestamps();
    }
}
