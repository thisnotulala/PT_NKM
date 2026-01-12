<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMaterialOut extends Model
{
    protected $table = 'project_material_outs';

    protected $fillable = [
        'project_id',
        'project_material_id',
        'progress_log_id',
        'tanggal',
        'qty_keluar',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'qty_keluar' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projectMaterial()
    {
        return $this->belongsTo(ProjectMaterial::class, 'project_material_id');
    }
}
