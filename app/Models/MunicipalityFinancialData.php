<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MunicipalityFinancialData extends Model
{
    use HasFactory;

    protected $fillable = [
        'municipality', 
        'time_period', 
        'link', 
        'population', 
        'size', 
        'notes'
    ];
}