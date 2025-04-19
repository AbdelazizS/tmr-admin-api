<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Landmark extends Model
{
    // use HasFactory;
    protected $fillable = ['name', 'distance'];
    public function neighborhood() {
        return $this->belongsTo(Neighborhood::class);
    }
}