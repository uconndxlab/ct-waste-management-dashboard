<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year',
        'bulky_waste',
        'recycling',
        'tipping_fees',
        'admin_costs',
        'hazardous_waste',
        'contractual_services',
        'landfill_costs',
        'total_sanitation_refuse',
        'only_public_works',
        'transfer_station_wages',
        'hauling_fees',
        'curbside_pickup_fees',
        'waste_collection',
        'notes',
    ];

    public function populations()
    {
        return $this->hasMany(Population::class, 'municipality', 'name');
    }

    public function latestPopulation()
    {
        return $this->hasOne(Population::class, 'municipality', 'name')
                    ->orderBy('year', 'desc');
    }
}
