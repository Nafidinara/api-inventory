<?php

namespace App;
use App\Pegawai;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $fillable = [
        'name'
    ];
    protected $primaryKey = 'divisi_id';

    public function pegawais(){
        return $this->hasMany(Pegawai::class);
    }
}
