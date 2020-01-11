<?php

namespace App;
use App\Laptop;
use App\Printer;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'type', 'path'
    ];

    protected $hidden = ['updated_at', 'created_at'];

    protected $primaryKey = 'file_id';

    public function laptops()
    {
        return $this->belongsToMany(Laptop::class);
    }

    public function printers()
    {
        return $this->belongsToMany(Printer::class);
    }
}
