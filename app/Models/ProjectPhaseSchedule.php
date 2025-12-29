<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhaseSchedule extends Model
{
    protected $fillable = [
        'project_id',
        'project_phase_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_hari',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function phase()
    {
        return $this->belongsTo(ProjectPhase::class, 'project_phase_id');
    }
}
