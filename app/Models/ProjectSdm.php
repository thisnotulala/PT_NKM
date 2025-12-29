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

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sdm()
    {
        return $this->belongsTo(Sdm::class);
    }
}
