<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\EventAttendee;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
            // 'event_id' => 'required | integer | unique:App\Models\EventAttendee,event_id',
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

            // $eventAttendee = EventAttendee::with('event')->where('user_id', $request->user_id)->get();
            // $eventData = $events->map(function ($event) {
                // $eventWithMedia = $latestEventAttendeeWithEvent->load('media');
                // return [
                //     'id' => $latestEventAttendeeWithEvent->id,
                //     'name' => $latestEventAttendeeWithEvent->event->name,
                //     'description' => $latestEventAttendeeWithEvent->event->description,
                //     'date_sched_start' => $latestEventAttendeeWithEvent->event->date_sched_start,
                //     'date_sched_end' => $latestEventAttendeeWithEvent->event->date_sched_end,
                //     'date_reg_deadline' => $latestEventAttendeeWithEvent->event->date_reg_deadline,
                //     'est_attendants' => $latestEventAttendeeWithEvent->event->est_attendants,
                //     'location' => $latestEventAttendeeWithEvent->event->location,
                //     'category_id' => $latestEventAttendeeWithEvent->event->category, // Use the actual foreign key field
                //     'venue_id' => $latestEventAttendeeWithEvent->event->venue, // Use the actual foreign key field
                //     'event_status' => $latestEventAttendeeWithEvent->event->event_status,
                //     'user_id' => $latestEventAttendeeWithEvent->user_id,
                //     'media' => $latestEventAttendeeWithEvent->media->map(function ($media) {
                //         return [
                //             'id' => $media->id,
                //             'file_name' => $media->file_name,
                //             'url' => $media->getUrl(), // Get the URL of the media
                //             // Add more attributes if needed
                //         ];
                //     }),
                // ];
            // });
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
                // 'eventAttendee' => $latestEventAttendeeWithEvent
                'eventAttendee' => $eventWithMedia

                // 'eventAttendee' => [
                //     'id' => $eventAttendee->id,
                //     'name' => $eventAttendee->event->name,
                //     'description' => $eventAttendee->event->description,
                //     'date_sched_start' => $eventAttendee->event->date_sched_start,
                //     'date_sched_end' => $eventAttendee->event->date_sched_end,
                //     'date_reg_deadline' => $eventAttendee->event->date_reg_deadline,
                //     'est_attendants' => $eventAttendee->event->est_attendants,
                //     'location' => $eventAttendee->event->location,
                //     'category_id' => $eventAttendee->event->category, // Use the actual foreign key field
                //     'venue_id' => $eventAttendee->event->venue, // Use the actual foreign key field
                //     'event_status' => $eventAttendee->event->event_status,
                //     'user_id' => $eventAttendee->user_id,
                //     'media' => $eventAttendee->media->map(function ($media) {
                //         return [
                //             'id' => $media->id,
                //             'file_name' => $media->file_name,
                //             'url' => $media->getUrl(), // Get the URL of the media
                //             // Add more attributes if needed
                //         ];
                //     }),
                // ]
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
        //
        // $eventAttendee = EventAttendee::find($id);  
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

    public function getByUserId(int $user_id)
    {
        // $eventAttendees = EventAttendee::with('event')->where('user_id',$user_id)->get();
        $eventAttendees = EventAttendee::with('event.media') // Eager load event and its media
        ->where('user_id', $user_id)
        ->get();

        // if($eventAttendees){
        //     return response()->json([
        //         'status' => 'success',
        //         'eventAttendees' => $eventAttendees
        //     ]);
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
                'eventAttendees' => $formattedEventAttendees
            ]);
        }else{
            return response()->json([
                'message' => 'No event attendees found for this user'
            ]);
        }
    }
}
