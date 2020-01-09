<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Pegawai;
use App\File;
class Laptop extends Model
{

    protected $fillable = [
        'PIC', 'departmen', 'serial_number','type',
        'inventaris_code','operating_system','pegawai_id'
    ];

    protected $primaryKey = 'laptop_id';

    public function users(){
        return $this->belongsTo(Pegawai::class);
    }

    public function files(){
        return $this->belongsToMany(File::class);
    }
}
