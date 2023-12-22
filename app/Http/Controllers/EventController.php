<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EventController extends Controller
{
    
    /**
     * All Venues
     * 
     * Shows all venues
     * @response 200 {"status": "success","posts": [{"id": 4,"user_id": 10,"text": "Good Evening!","created_at": "2023-10-09T08:21:34.000000Z","updated_at": "2023-10-09T08:22:25.000000Z"}]}
     * 
     */
    public function index()
    {
        //$events = Event::all();
        $events = Event::where('event_status', 1)->paginate(10);

        if($events -> count() >0){
            $eventData = $events->map(function ($event) {
                $eventWithMedia = $event->load('media');
                return [
                    'id' => $eventWithMedia->id,
                    'name' => $eventWithMedia->name,
                    'description' => $eventWithMedia->description,
                    'date_sched_start' => $eventWithMedia->date_sched_start,
                    'date_sched_end' => $eventWithMedia->date_sched_end,
                    'date_reg_deadline' => $eventWithMedia->date_reg_deadline,
                    'est_attendants' => $eventWithMedia->est_attendants,
                    'location' => $eventWithMedia->location,
                    'category_id' => $eventWithMedia->category, // Use the actual foreign key field
                    'venue_id' => $eventWithMedia->venue, // Use the actual foreign key field
                    'event_status' => $eventWithMedia->event_status,
                    'user_id' => $eventWithMedia->user_id,
                    'media' => $eventWithMedia->media->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'file_name' => $media->file_name,
                            'url' => $media->getUrl(), // Get the URL of the media
                            // Add more attributes if needed
                        ];
                    }),
                ];
            });
    
            return response()->json([
                'status' => 'success',
                'events' => $eventData,
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'total' => $events->total(),
                    'per_page' => $events->perPage(),
                ]
            ]);
        }else{
            return response()->json([
                'message' => 'events empty'
            ]);
        }
    }

    /**
     * Create Post
     * 
     * Creates a post
     * @bodyParam user_id int required user_id
     * @bodyParam text string required text
     * 
     * @response 200 {"message":"Post added successfully","post":[{"id":4,"user_id":10,"text":"Good Evening!","created_at":"2023-10-09T08:21:34.000000Z","updated_at":"2023-10-09T08:22:25.000000Z"},{"id":5,"user_id":3,"text":"Good Morning!","created_at":"2023-10-10T01:08:30.000000Z","updated_at":"2023-10-10T01:08:30.000000Z"}]}
     */
    public function store(Request $request)
    {
        //
        
        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required | max:166',
            'date_sched_start' => 'required',
            'date_sched_end' => 'required',
            'date_reg_deadline' => 'required',
            'est_attendants' => 'required | integer',
            'location' => 'required',
            'images' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'category_id' => 'required|integer',
            'venue_id' => 'required|integer',
            'event_status' => 'required|integer',
            'user_id' => 'required|integer', 
        ]);
        
        if ($validated->fails()) {
            return response()->json([
                'message' => $validated->messages()
            ]);
        }
    
        // Handle file upload
        if ($request->hasFile('images')) {
            $event = Event::create($request->except('images'));
    
            $event->addMediaFromRequest('images')
                ->toMediaCollection('banners'); // Use the collection name defined in the model
    
            $events = Event::with('media')->get(); // Optionally eager load media
    
            return response()->json([
                'message' => 'Event added successfully',
                'event' => $events
            ]);
        } else {
            return response()->json([
                'message' => 'No image provided.'
            ]);
        }
    }

    /**
     * Find Post
     * 
     * Find post by ID
     * @urlParam id int required post ID
     * @response 200 {"status":"success","post":{"id":4,"user_id":10,"text":"Good Evening!","created_at":"2023-10-09T08:21:34.000000Z","updated_at":"2023-10-09T08:22:25.000000Z"}}
     */
    public function show(string $id)
    {
        //
        // $event = Event::find($id);
        $event = Event::with('media')->where('event_status', 1)
              ->find($id);

        if($event){
            $eventData = [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'date_sched_start' => $event->date_sched_start,
                'date_sched_end' => $event->date_sched_end,
                'date_reg_deadline' => $event->date_reg_deadline,
                'est_attendants' => $event->est_attendants,
                'location' => $event->location,
                'category_id' => $event->category,
                'venue_id' => $event->venue,
                'event_status' => $event->event_status,
                'user_id' => $event->user_id
            ];
    
            $mediaData = $event->getMedia('banners')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(), // Get the URL of the media
                    // Add more attributes if needed
                ];
            });
    
            return response()->json([
                'status' => 'success',
                'event' => $eventData,
                'media' => $mediaData,
            ]);
        }else{
            return response()->json([
                'message' => 'No event found'
            ]);
        }
    }

    public function showAllByUserId(string $user_id)
    {
        //
        // $event = Event::find($id);
        $events = Event::with('media')
        ->where('user_id', $user_id) // Filter events by user_id
        ->get(); // Use get() instead of find()

        if ($events->isNotEmpty()) {
            $eventData = $events->map(function ($event) {
                $eventWithMedia = $event->load('media');
                return [
                    'id' => $eventWithMedia->id,
                    'name' => $eventWithMedia->name,
                    'description' => $eventWithMedia->description,
                    'date_sched_start' => $eventWithMedia->date_sched_start,
                    'date_sched_end' => $eventWithMedia->date_sched_end,
                    'date_reg_deadline' => $eventWithMedia->date_reg_deadline,
                    'est_attendants' => $eventWithMedia->est_attendants,
                    'location' => $eventWithMedia->location,
                    'category_id' => $eventWithMedia->category, // Use the actual foreign key field
                    'venue_id' => $eventWithMedia->venue, // Use the actual foreign key field
                    'event_status' => $eventWithMedia->event_status,
                    'user_id' => $eventWithMedia->user_id,
                    'media' => $eventWithMedia->media->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'file_name' => $media->file_name,
                            'url' => $media->getUrl(), // Get the URL of the media
                            // Add more attributes if needed
                        ];
                    }),
                ];
            });
           
    
            return response()->json([
                'status' => 'success',
                'events' => $eventData
            ]);

        } else {
            return response()->json([
                'message' => 'No events found for this user'
            ]);
        }
    }

    public function showAllByUserIdAndStatus(string $user_id, int $event_status)
    {
        //
        // $event = Event::find($id);
        $events = Event::with('media')
        ->where('user_id', $user_id) // Filter events by user_id
        ->where('event_status', $event_status)
        ->paginate(5); // Use get() instead of find()

        if ($events->isNotEmpty()) {
            $eventData = $events->map(function ($event) {
                $eventWithMedia = $event->load('media');
                return [
                    'id' => $eventWithMedia->id,
                    'name' => $eventWithMedia->name,
                    'description' => $eventWithMedia->description,
                    'date_sched_start' => $eventWithMedia->date_sched_start,
                    'date_sched_end' => $eventWithMedia->date_sched_end,
                    'date_reg_deadline' => $eventWithMedia->date_reg_deadline,
                    'est_attendants' => $eventWithMedia->est_attendants,
                    'location' => $eventWithMedia->location,
                    'category_id' => $eventWithMedia->category, // Use the actual foreign key field
                    'venue_id' => $eventWithMedia->venue, // Use the actual foreign key field
                    'event_status' => $eventWithMedia->event_status,
                    'user_id' => $eventWithMedia->user_id,
                    'media' => $eventWithMedia->media->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'file_name' => $media->file_name,
                            'url' => $media->getUrl(), // Get the URL of the media
                            // Add more attributes if needed
                        ];
                    }),
                ];
            });
           
    
            return response()->json([
                'status' => 'success',
                'events' => $eventData,
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'total' => $events->total(),
                    'per_page' => $events->perPage(),
                ]
            ]);
            
        } else {
            return response()->json([
                'message' => 'No events found for this user'
            ]);
        }
    }
    /**
     * Update Post
     * 
     * update post by ID
     * @urlParam id integer required ID of post.
     * 
     * @response 200 {"message":"Post updated successfully","post":{"id":4,"user_id":"10","text":"Good Evening!","created_at":"2023-10-09T08:21:34.000000Z","updated_at":"2023-10-09T08:22:25.000000Z"}}
     */
    public function update(Request $request, string $id)
    {
        //
        $validated = Validator::make($request->all(),[
            'name' => 'required',
            'description' => 'required | max:166',
            'date_sched_start' => 'required',
            'date_sched_end' => 'required',
            'date_reg_deadline' => 'required',
            'est_attendants' => 'required | integer',
            'location' => 'required',
            'images' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048', 
            'category_id' => 'required|integer',
            'venue_id' => 'required|integer',
            'event_status' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if($validated -> fails()){
            return response()->json([
                'message' => $validated->messages()
            ]);
        }else{
            $event = Event::find($id);
            
            if ($event) {
                # code...
                $event->update($request->all());

                return response()->json([
                    'message' => 'event updated successfully',
                    'event' => $event
                ]);
            }else{
                return response()->json([
                    'message' => 'No event found'
                ]); 
            }

            
        }
    }

    /**
     * Delete Post
     * 
     * Deletes post by ID
     * @urlParam id integer required ID of post.
     * @response 200 {"message":"Post deleted successfully","remaining posts":[{"id":5,"user_id":3,"text":"Good Morning!","created_at":"2023-10-10T01:08:30.000000Z","updated_at":"2023-10-10T01:08:30.000000Z"},{"id":6,"user_id":3,"text":"Good Morning!","created_at":"2023-10-10T01:20:08.000000Z","updated_at":"2023-10-10T01:20:08.000000Z"}]}
     */
    public function delete(string $id)
    {
        //
        $event = Event::find($id);
        if($event){
            $event->delete();
            $events = Event::all();
            return response()->json([
                'message' => 'events deleted successfully',
                'remaining events' => $events
            ]);
        }else{
            return response()->json([
                'message' => 'No event found'
            ]);
        }
    }

}
