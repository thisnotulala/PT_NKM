<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    protected $fillable = [
        'project_id','tanggal','kategori','nominal','keterangan',
        'sdm_id','equipment_id','bukti_path','created_by'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sdm()
    {
        return $this->belongsTo(Sdm::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
