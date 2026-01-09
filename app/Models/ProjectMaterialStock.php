<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMaterialStock extends Model
{
    protected $table = 'project_material_stocks';

    protected $fillable = [
        'project_id',
        'project_material_id',
        'tanggal',
        'qty_masuk',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'qty_masuk' => 'decimal:2',
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
