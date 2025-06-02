<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TownClassification extends Model
{
    use HasFactory;

    protected $fillable = [
        'municipality', 'region_type', 'geographical_region', 'county'
    ];
}