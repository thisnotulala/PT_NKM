<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'nama_proyek',
        'client_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'dokumen',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function phases()
    {
        return $this->hasMany(ProjectPhase::class);
    }
    public function sdms()
    {
        return $this->belongsToMany(\App\Models\Sdm::class, 'project_sdm')
            ->withTimestamps();
    }

}