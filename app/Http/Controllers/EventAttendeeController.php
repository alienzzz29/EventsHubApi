<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\EventAttendee;

class EventAttendeeController extends Controller
{
    //
    /**
     * All Venues
     * 
     * Shows all venues
     * @response 200 {"status": "success","posts": [{"id": 4,"user_id": 10,"text": "Good Evening!","created_at": "2023-10-09T08:21:34.000000Z","updated_at": "2023-10-09T08:22:25.000000Z"}]}
     * 
     */
    public function index()
    {
        //
        $eventAttendees = EventAttendee::all();

        if($eventAttendees -> count() >0){
            return response()->json([
                'status' => 'success',
                'eventAttendees' => $eventAttendees
            ]);
        }else{
            return response()->json([
                'message' => 'event attendees empty'
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
            'user_id' => 'required | integer',
            'event_id' => 'required | integer',
        ]);

        if($validated -> fails()){
            return response()->json([
                'message' => $validated->messages()
            ]);
        }else{
            EventAttendee::create($request->all());

            $eventAttendees = EventAttendee::all();

            return response()->json([
                'message' => 'Event Attendee added successfully',
                'eventAttendee' => $eventAttendees
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
        $eventAttendee = EventAttendee::find($id);

        if($eventAttendee){
            return response()->json([
                'status' => 'success',
                'eventAttendee' => $eventAttendee
            ]);
        }else{
            return response()->json([
                'message' => 'No event attendee found'
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
            'user_id' => 'required | integer',
            'event_id' => 'required | integer',
        ]);

        if($validated -> fails()){
            return response()->json([
                'message' => $validated->messages()
            ]);
        }else{
            $eventAttendee = EventAttendee::find($id);
            
            if ($eventAttendee) {
                # code...
                $eventAttendee->update($request->all());

                return response()->json([
                    'message' => 'Event Attendee updated successfully',
                    'eventAttendee' => $eventAttendee
                ]);
            }else{
                return response()->json([
                    'message' => 'No Event Attendee found'
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
        $eventAttendee = EventAttendee::find($id);
        if($eventAttendee){
            $eventAttendee->delete();
            $eventAttendees = EventAttendee::all();
            return response()->json([
                'message' => 'Event Attendee deleted successfully',
                'remaining eventAttendees' => $eventAttendees
            ]);
        }else{
            return response()->json([
                'message' => 'No Event Attendee found'
            ]);
        }
    }

}
