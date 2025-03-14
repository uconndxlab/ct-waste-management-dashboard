<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverallTownInfo extends Model
{
    use HasFactory;

    protected $table = 'overall_town_info';

    protected $fillable = [
        'municipality', 'department', 'contact_1', 'title_1', 'phone_1', 'email_1',
        'contact_2', 'title_2', 'phone_2', 'email_2', 'notes', 'other_useful_notes'
    ];
}
