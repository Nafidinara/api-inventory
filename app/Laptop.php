<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Pegawai;
use App\File;
class Laptop extends Model
{

    protected $fillable = [
        'PIC', 'divisi', 'serial_number','type',
        'inventaris_code','operating_system','pegawai_id'
    ];

    protected $hidden = ['updated_at', 'created_at'];

    protected $primaryKey = 'laptop_id';

    public function pegawais(){
        return $this->belongsTo(Pegawai::class);
    }

    public function files(){
        return $this->belongsToMany(File::class);
    }
}
