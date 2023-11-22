<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;

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
        $events = Event::where('is_enabled', true)->paginate(10);

        if($events -> count() >0){
            $eventData = $events->map(function ($event) {
                return [
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
                    'is_enabled' => $event->is_enabled,
                    'user_id' => $event->user_id
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
            'category_id' => 'required|integer',
            'venue_id' => 'required|integer',
            'is_enabled' => 'required|boolean',
            'user_id' => 'required|integer',
        ]);

        if($validated -> fails()){
            return response()->json([
                'message' => $validated->messages()
            ]);
        }else{
            Event::create($request->all());

            $events = event::all();

            return response()->json([
                'message' => 'event added successfully',
                'event' => $events
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
        $event = Event::find($id);

        if($event){
            return response()->json([
                'status' => 'success',
                // 'event' => $event
                'event' => [
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
                    'is_enabled' => $event->is_enabled,
                    'user_id' => $event->user_id

                ]
            ]);
        }else{
            return response()->json([
                'message' => 'No event found'
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
            'category_id' => 'required|integer',
            'venue_id' => 'required|integer',
            'is_enabled' => 'required|boolean',
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
