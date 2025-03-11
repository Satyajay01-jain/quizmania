<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;
    protected $table="users";
    
    public function payments()
    {
        return $this->hasOne('PaymentModel'); // links this->id to events.course_id
    }
    
}
