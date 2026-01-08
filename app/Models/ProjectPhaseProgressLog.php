<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhaseProgressLog extends Model
{
    protected $table = 'project_phase_progress_logs';

    protected $fillable = [
        'project_id',
        'project_phase_id',
        'tanggal_update',
        'progress',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_update' => 'date',
        'progress' => 'integer',
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

    // ✅ SDM yang bekerja pada log ini
    public function sdms()
    {
        return $this->belongsToMany(
            \App\Models\Sdm::class,
            'project_phase_progress_log_sdms',
            'log_id',
            'sdm_id'
        )->withTimestamps();
    }

    public function materialUsages()
    {
        return $this->hasMany(\App\Models\ProjectMaterialUsage::class, 'progress_log_id');
    }

}
