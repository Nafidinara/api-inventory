<?php

namespace App;
use App\Laptop;
use App\Divisi;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{

    protected $fillable = [
        'name','divisi_id'
    ];

    protected $primaryKey = 'pegawai_id';

    public function laptop(){
        return $this->hasMany(Laptop::class);
    }

    public function divisi(){
        return $this->belongsTo(Divisi::class);
    }
}
