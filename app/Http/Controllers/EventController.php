<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Carbon\Carbon; 
use App\Models\Category;
use App\Models\Venue;

class EventController extends Controller
{
  
    /**
     * All Venues
     * 
     * Shows all venues
     * @response 200 {"status": "success","posts": [{"id": 4,"user_id": 10,"text": "Good Evening!","created_at": "2023-10-09T08:21:34.000000Z","updated_at": "2023-10-09T08:22:25.000000Z"}]}
     * 
     */
    public function index(Request $request)
    {
        //$events = Event::all();
        $events_query = Event::where('event_status', 1);
        
        if($request->keyword){
            $events_query->where('name','LIKE','%'.$request->keyword.'%');
        }

        if($request->has('sort') && $request->sort === 'regDeadlineAsc'){
            $events_query->orderBy('date_reg_deadline','asc');
        }
        if($request->has('sort') && $request->sort === 'regDeadlineDesc'){
            $events_query->orderBy('date_reg_deadline','desc');
        }

        if($request->has('sort') && $request->sort === 'nameAsc'){
            $events_query->orderBy('name','asc');
        }
        if($request->has('sort') && $request->sort === 'nameDesc'){
            $events_query->orderBy('name','desc');
        }
        if($request->has('attendance')){
            $attendanceRanges = explode(',', $request->query('attendance'));
        
            // Build query conditions based on the provided attendance ranges
            foreach ($attendanceRanges as $range) {
                // Explode each range to get its lower and upper limits
                $limits = explode('And', $range);

                if (count($limits) > 1) {
                    if ($limits[0] === 'under') {
                        // Example: If 'under500' is present in the URL parameter
                        $events_query->where('est_attendants', '<=', intval($limits[1]));
                    } else {
                        // Example: If 'between500And1000' is present in the URL parameter
                        $lower = intval($limits[0]);
                        $upper = intval($limits[1]);
                        $events_query->whereBetween('est_attendants', [$lower, $upper]);
                    }
                } else {
                    // Handle the case where the array doesn't have an index 1 (or is not in the expected format)
                    // For instance, log an error or handle it based on your requirements
                }
            }
        
        }
        if($request->has('venues')){
            $venueIds = explode(',', $request->query('venues'));
            $events_query->whereIn('venue_id', $venueIds);
        }
        if($request->has('categories')){
            $categoryIds = explode(',', $request->query('categories'));
            $events_query->whereIn('category_id', $categoryIds);
        }

        if ($request->has('dateStart') && $request->has('dateEnd')) {
            $dateStart = $request->query('dateStart');
            $dateEnd = $request->query('dateEnd');
        
            $events_query->where(function($query) use ($dateStart, $dateEnd) {
                $query->whereBetween('date_sched_start', [$dateStart, $dateEnd])
                      ->orWhereBetween('date_sched_end', [$dateStart, $dateEnd]);
            });
        }

        // if($request->keyword){
        //     $events_query->where('name','LIKE','%'.$request->keyword.'%');
        // }
        // $events_query->where('event_status', 1);

        $events = $events_query->paginate(10);

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

    public function indexAdmin(Request $request)
    {
        //$events = Event::all();
        $events_query = Event::whereIn('event_status', [0, 1]);
        // $events = Event::whereIn('event_status', [0, 1])->paginate(10);

        if ($request->keyword) {
            $events_query->where('name','LIKE','%'.$request->keyword.'%');
        }

        $events = $events_query->paginate(10);

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
                    'user_id' => $eventWithMedia->user,
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
    public function showByIdAuth(string $id)
    {
        //
        // $event = Event::find($id);
        $event = Event::with('media')->find($id);

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

    public function showAllByUserIdAndStatus(string $user_id, int $event_status, Request $request)
    {
        //
        // $event = Event::find($id);
        // $events = Event::with('media')
        // ->where('user_id', $user_id) // Filter events by user_id
        // ->where('event_status', $event_status)
        // ->paginate(5); // Use get() instead of find()
        
        $event_query = Event::with('media')
        ->where('user_id', $user_id) // Filter events by user_id
        ->where('event_status', $event_status);

        if($request->keyword){
            $event_query->where('name','LIKE','%'.$request->keyword.'%');
        }

        $events = $event_query->paginate(5); 

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
            // if ($request->hasFile('images')) {
            //     $event = Event::create($request->except('images'));
        
            //     $event->addMediaFromRequest('images')
            //         ->toMediaCollection('banners'); // Use the collection name defined in the model
        
            //     $events = Event::with('media')->get(); // Optionally eager load media
        
            //     return response()->json([
            //         'message' => 'Event added successfully',
            //         'event' => $events
            //     ]);
            // } else {
            //     return response()->json([
            //         'message' => 'No image provided.'
            //     ]);
            // }
            $event = Event::find($id);
            
            if ($event) {
                # code...
                // $event->update($request->all());
                if ($request->hasFile('images')) {
                    $event->update($request->except('images')); // Update event details
                    
                    $event->clearMediaCollection('banners'); // Clear existing media
                    
                    $event->addMediaFromRequest('images')
                        ->toMediaCollection('banners'); // Add new images to the media collection
                } else {
                    $event->update($request->all()); // Update event details without changing images
                }

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

    public function updateStatus(Request $request, string $id)
    {
        //
        $validated = Validator::make($request->all(),[
            'event_status' => 'required|integer'
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
        // //
        // $event = Event::find($id);
        // if($event){
        //     $event->delete();
        //     $events = Event::all();
        //     return response()->json([
        //         'message' => 'events deleted successfully',
        //         'remaining events' => $events
        //     ]);
        // }else{
        //     return response()->json([
        //         'message' => 'No event found'
        //     ]);
        // }
        $event = Event::find($id);
        if($event){
            // Detach relationships from event_attendee table
            $event->eventAttendees()->delete();
    
            // Delete the event
            $event->delete();
    
            // Fetch remaining events
            $events = Event::all();
            return response()->json([
                'message' => 'Event deleted successfully',
                'remaining_events' => $events
            ]);
        } else {
            return response()->json([
                'message' => 'No event found'
            ]);
        }
    }

    public function getTotalEvents()
    {
      $totalEvents = Event::count();

      // Calculate the start and end dates for the last month
      $startDate = Carbon::now()->subMonth()->startOfMonth();
      $endDate = Carbon::now()->subMonth()->endOfMonth();
  
      // Get the total number of events within the last month
      $totalEventsLastMonth = Event::whereBetween('created_at', [$startDate, $endDate])->count();
  
      // Get the total number of events for all time
      $totalEventsAllTime = Event::count();
  
      // Calculate the increase
      $increase = $totalEventsAllTime - $totalEventsLastMonth;
  
      // Calculate the percentage increase
      $percentageIncrease = ($totalEventsLastMonth > 0) ? round(($increase / $totalEventsLastMonth) * 100, 2): 0;
  

        return response()->json([
            'total_events' => $totalEvents,
            'percentage_increase_since_last_month' => $percentageIncrease
        ]);
    }

    public function getTotalEventsByCategory()
    {
        try {
            $categories = Category::all();

            $data = [];

            foreach ($categories as $category) {
                $totalEvents = Event::where('category_id', $category->id)
                    ->where('event_status', 1)
                    ->count();

                $data[] = [
                    'category' => $category->name,
                    'total_events' => $totalEvents,
                ];
            }

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching data'], 500);
        }
    }

    public function getTotalEventsByVenue()
    {
        try {
            $venues = Venue::all();

            $data = [];

            foreach ($venues as $venue) {
                $totalEvents = Event::where('venue_id', $venue->id)
                    ->where('event_status', 1)
                    ->count();

                $data[] = [
                    'venue' => $venue->name,
                    'total_events' => $totalEvents,
                ];
            }

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching data'], 500);
        }
    }

    public function topEventsByAttendance()
    {
        try {
            $events = Event::where('event_status', 1)
                ->withCount('eventAttendees') // Count the actual attendees
                ->orderBy('event_attendees_count', 'desc') // Order by the actual number of attendees
                ->take(10) // Get the top 10 events
                ->with('eventAttendees') // Load the eventAttendees relationship
                ->get();

            $eventData = $events->map(function ($event) {
                $eventWithMedia = $event->load('media');
                return [
                    'id' => $eventWithMedia->id,
                    'name' => $eventWithMedia->name,
                    'attendees_count' => $event->event_attendees_count,
                    // 'event_attendees' => $event->eventAttendees->map(function ($eventAttendee) {
                    //     return [
                    //         'user_id' => $eventAttendee->user_id,
                    //         // 'attendee_name' => $eventAttendee->attendee_name,
                    //         // Add more attributes if needed
                    //     ];
                    // }),
                ];
            });

            return response()->json(['data' => $eventData]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching data'], 500);
        }
    }




}
