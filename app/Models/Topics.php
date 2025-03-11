<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Questions;

class Topics extends Model
{
    use HasFactory;
    protected $table="topics";
    // this for updating topic id 
    public function questions()
    {
        return $this->belongsToMany(Questions::class, 'question_topic_mapping','question_id', 'topic_id');
    }
    public function question()
    {
        return $this->hasMany(Questions::class);
    }
}
