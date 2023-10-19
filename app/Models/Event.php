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
        'date',
        'location',
        'category_id',
        'venue_id',
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
