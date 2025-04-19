<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    // use HasFactory;

    protected $fillable = ['description'];
    public function property() {
        return $this->belongsTo(Property::class);
    }
    
    public function landmarks() {
        return $this->hasMany(Landmark::class);
    }
    
}