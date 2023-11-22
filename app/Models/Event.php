<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'date_sched_start',
        'date_sched_end',
        'date_reg_deadline',
        'est_attendants',
        'location',
        'category_id',
        'venue_id',
        'is_enabled',
        'user_id'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    
    public function venue(){
        return $this->belongsTo(Venue::class);
    }
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    
    public function eventAttendees(){
        return $this->hasMany(eventAttendee::class);
    }
}
