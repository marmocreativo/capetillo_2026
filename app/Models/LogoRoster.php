<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogoRoster extends Model
{
    use HasFactory;

    protected $table = 'logos_roster';

    protected $fillable = ['image'];
}