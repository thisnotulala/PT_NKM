<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMaterialUsage extends Model
{
    protected $fillable = [
        'progress_log_id',
        'project_material_id',
        'qty_pakai',
    ];

    public function log()
    {
        return $this->belongsTo(ProjectPhaseProgressLog::class, 'progress_log_id');
    }

    public function material()
    {
        return $this->belongsTo(ProjectMaterial::class, 'project_material_id');
    }
}
