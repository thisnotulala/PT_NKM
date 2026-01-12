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
        'harga',        
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

    public function stocks()
    {
        return $this->hasMany(ProjectMaterialStock::class, 'project_material_id');
    }
    // App\Models\ProjectMaterial.php

    public function outs()
    {
        return $this->hasMany(\App\Models\ProjectMaterialOut::class, 'project_material_id');
    }

}
