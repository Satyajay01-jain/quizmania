<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Topics;
use App\Models\ContestTypes;
class Questions extends Model
{
    use HasFactory;
    protected $protected="questions";
    
    // this for updating topic id 
    public function topics()
    {
        return $this->belongsToMany(Topics::class, 'question_topic_mapping','question_id', 'topic_id');
    }
    public function topic()
    {
        return $this->belongsTo(Topics::class);
    }
    public function ContestTypes()
    {
        return $this->belongsTo(ContestTypes::class);
    }
    public function getStatusAttribute($value)
    {
        return $value == 1 ? 'Active' : 'Inactive'; // Customize as needed
    }
}
