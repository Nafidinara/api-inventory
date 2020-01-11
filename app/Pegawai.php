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

    protected $hidden = ['updated_at', 'created_at'];

    protected $primaryKey = 'pegawai_id';

    public function laptops(){
        return $this->hasMany(Laptop::class);
    }

    public function divisi(){
        return $this->belongsTo(Divisi::class);
    }
}
