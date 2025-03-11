<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestTypes extends Model
{
    use HasFactory;
    
    
    public function Question()
    {
        return $this->hasMany(Questions::class);
    }
}
