<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhase extends Model
{
    protected $fillable = [
        'project_id',
        'nama_tahapan',
        'persen',
        'urutan',
        'progress',
        'last_progress_at',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function schedule()
    {
        return $this->hasOne(\App\Models\ProjectPhaseSchedule::class, 'project_phase_id');
    }

    public function progressLogs()
    {
        return $this->hasMany(\App\Models\ProjectPhaseProgressLog::class, 'project_phase_id');
    }

}
