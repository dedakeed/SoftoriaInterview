<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public $timestamps = false;
    protected $table = 'locations';
    use HasFactory;
    protected $guarded =[];

    protected $casts = [
        'location_code' => 'integer',
        'location_code_parent' => 'integer',
        'keywords' => 'integer',
        'serps' => 'integer',
    ];
}
