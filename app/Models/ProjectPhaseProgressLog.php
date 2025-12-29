<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhaseProgressLog extends Model
{
    protected $fillable = [
        'project_id','project_phase_id','tanggal_update','progress','catatan','created_by'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function phase()
    {
        return $this->belongsTo(ProjectPhase::class, 'project_phase_id');
    }

    public function photos()
    {
        return $this->hasMany(ProjectPhaseProgressPhoto::class, 'log_id');
    }
}
