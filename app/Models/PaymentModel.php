<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users;

class PaymentModel extends Model
{
    use HasFactory;
    protected $table="payments";
    
    public function users()
    {
        //return $this->belongsTo('Users'); // links this->course_id to courses.id
    }
}
