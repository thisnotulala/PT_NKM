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
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
