<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMaterial extends Model
{
    protected $fillable = [
        'project_id',
        'nama_material',
        'satuan',
        'qty_estimasi',
        'toleransi_persen',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function usages()
    {
        return $this->hasMany(ProjectMaterialUsage::class, 'project_material_id');
    }
}
