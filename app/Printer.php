<?php

namespace App;
use App\File;

use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    protected $fillable = [
        'divisi','type'
    ];

    protected $hidden = ['updated_at', 'created_at'];

    protected $primaryKey = 'printer_id';

    public function files(){
        return $this->belongsToMany(File::class);
    }
}
