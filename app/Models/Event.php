<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Event extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
        'event_status',
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
    
    public function eventAttendees()
    {
        return $this->hasMany(EventAttendee::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banners')
            ->singleFile(); // Adjust the collection name and configuration as needed
    }

}
