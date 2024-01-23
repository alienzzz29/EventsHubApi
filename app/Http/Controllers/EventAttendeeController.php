<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\EventAttendee;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;

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
            'event_id' => 'required|integer|unique:event_attendees,event_id,NULL,id,user_id,' . $request->user_id,
        ]);

        if($validated -> fails()){
            return response()->json([
                'message' => $validated->messages()
            ]);
        }else{
            $eventAttendee = EventAttendee::create($request->all());

            $latestEventAttendeeWithEvent = EventAttendee::with('event')
            ->where('id', $eventAttendee->id) // Filter by the newly created EventAttendee's ID
            ->first();
            if ($latestEventAttendeeWithEvent) {
                $eventWithMedia = $latestEventAttendeeWithEvent->event->load('media');
            
                return [
                    'id' => $latestEventAttendeeWithEvent->event->id,
                    'name' => $latestEventAttendeeWithEvent->event->name,
                    'description' => $latestEventAttendeeWithEvent->event->description,
                    'date_sched_start' => $latestEventAttendeeWithEvent->event->date_sched_start,
                    'date_sched_end' => $latestEventAttendeeWithEvent->event->date_sched_end,
                    'date_reg_deadline' => $latestEventAttendeeWithEvent->event->date_reg_deadline,
                    'est_attendants' => $latestEventAttendeeWithEvent->event->est_attendants,
                    'location' => $latestEventAttendeeWithEvent->event->location,
                    'category_id' => $latestEventAttendeeWithEvent->event->category,
                    'venue_id' => $latestEventAttendeeWithEvent->event->venue,
                    'event_status' => $latestEventAttendeeWithEvent->event->event_status,
                    'user_id' => $latestEventAttendeeWithEvent->user_id,
                    'media' => $latestEventAttendeeWithEvent->event->media->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'file_name' => $media->file_name,
                            'url' => $media->getUrl(),
                            // Add more attributes if needed
                        ];
                    }),
                ];
            }

            return response()->json([
                'message' => 'Event Attendee added successfully',
                'eventAttendee' => $eventWithMedia
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
    public function bulkstore(Request $request)
    {
        // Validate the incoming request data for each attendee in the array
        $validated = Validator::make($request->all(), [
            'attendees' => 'required|array',
            'attendees.*.user_id' => 'required|integer',
            'attendees.*.event_id' => 'required|integer|unique:event_attendees,event_id,NULL,id,user_id,' . $request->input('attendees.*.user_id'),
            // Add more validation rules for other fields if needed
        ]);

        // Check if validation fails
        if ($validated->fails()) {
            return response()->json([
                'message' => $validated->messages()
            ], 422); // Return a 422 status code for validation errors
        } else {
            // Array to hold created attendees
            $createdAttendees = [];

            // Loop through each attendee in the array and create EventAttendee records
            foreach ($request->input('attendees') as $attendeeData) {
                $eventAttendee = EventAttendee::create($attendeeData);
                $createdAttendees[] = $eventAttendee;
            }

            // Fetch the created attendees with their associated events
            $attendeesWithEvents = EventAttendee::with('event')
                ->whereIn('id', collect($createdAttendees)->pluck('id')->toArray())
                ->get();

            // Return a success response with the details of the created event attendees
            return response()->json([
                'message' => 'Event Attendees added successfully',
                'attendees' => $attendeesWithEvents
            ], 201); // Return a 201 status code for successful creation
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
    public function delete(int $event_id, int $user_id)
    {
        $eventAttendee = EventAttendee::where('event_id', $event_id)->where('user_id', $user_id)->firstOrFail();  
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

    public function getByUserId(int $user_id, Request $request)
    {
        $eventAttendees_query = EventAttendee::with('event.media') // Eager load event and its media
        ->where('user_id', $user_id);

        if ($request->keyword) {
            $eventAttendees_query->whereHas('event', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        $eventAttendees = $eventAttendees_query->paginate(7);
        if ($eventAttendees->isNotEmpty()) {
            $formattedEventAttendees = $eventAttendees->map(function ($attendee) {
                return [
                    'id' => $attendee->event->id,
                    'name' => $attendee->event->name,
                    'description' => $attendee->event->description,
                    'date_sched_start' => $attendee->event->date_sched_start,
                    'date_sched_end' => $attendee->event->date_sched_end,
                    'date_reg_deadline' => $attendee->event->date_reg_deadline,
                    'est_attendants' => $attendee->event->est_attendants,
                    'location' => $attendee->event->location,
                    'category_id' => $attendee->event->category, // Use the actual foreign key field
                    'venue_id' => $attendee->event->venue, // Use the actual foreign key field
                    'event_status' => $attendee->event->event_status,
                    'user_id' => $attendee->event->user_id,
                    'media' => $attendee->event->media->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'file_name' => $media->file_name,
                            'url' => $media->getUrl(),
                            // Add more attributes if needed
                        ];
                    }),
                    // Add more attendee-related attributes if needed
                ];
            });
            return response()->json([
                'status' => 'success',
                'eventAttendees' => $formattedEventAttendees,
                'pagination' => [
                    'current_page' => $eventAttendees->currentPage(),
                    'per_page' => $eventAttendees->perPage(),
                    'total' => $eventAttendees->total(),
                    // Add more pagination-related details if needed
                ],
            ]);
        }else{
            return response()->json([
                'message' => 'No event attended for this user'
            ]);
        }
    }

    public function getByEventId(int $event_id, Request $request)
    {
        $keyword = $request->keyword;

        $eventAttendees_query = EventAttendee::where('event_id', $event_id);
        if($keyword){
            $eventAttendees_query->whereHas('user', function ($query) use ($keyword) {
                $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%' . $keyword . '%')
                      ->orWhere('email', 'LIKE', '%' . $keyword . '%');
            });
        }
        $eventAttendees = $eventAttendees_query->paginate(7);

        if ($eventAttendees->isNotEmpty()) {
            $formattedEventAttendees = $eventAttendees->map(function ($attendee) {
                return [
                    'id' => $attendee->user->id,
                    'first_name' => $attendee->user->first_name,
                    'last_name' => $attendee->user->last_name,
                    'email' => $attendee->user->email
            
                    // Add more attendee-related attributes if needed
                ];
            });
            return response()->json([
                'status' => 'success',
                'eventAttendees' => $formattedEventAttendees,
                'pagination' => [
                    'current_page' => $eventAttendees->currentPage(),
                    'per_page' => $eventAttendees->perPage(),
                    'total' => $eventAttendees->total(),
                    // Add more pagination-related details if needed
                ],
            ]);
        }else{
            return response()->json([
                'message' => 'No event attended for this user'
            ]);
        }

    }
}
