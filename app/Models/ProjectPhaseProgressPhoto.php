<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhaseProgressPhoto extends Model
{
    protected $fillable = ['log_id','photo_path'];

    public function log()
    {
        return $this->belongsTo(ProjectPhaseProgressLog::class, 'log_id');
    }
}
