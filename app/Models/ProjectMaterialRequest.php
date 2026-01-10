<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMaterialRequest extends Model
{
    protected $fillable = [
        'project_id',
        'project_material_id',
        'tanggal_pengajuan',
        'qty',
        'catatan',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'approval_note',
        'stock_id',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'approved_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projectMaterial()
    {
        return $this->belongsTo(ProjectMaterial::class);
    }

    public function stock()
    {
        return $this->belongsTo(ProjectMaterialStock::class, 'stock_id');
    }
}
