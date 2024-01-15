<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Venue;
use Carbon\Carbon;

class VenueController extends Controller
{
    /**
     * All Venues
     * 
     * Shows all venues
     * @response 200 {"status": "success","posts": [{"id": 4,"user_id": 10,"text": "Good Evening!","created_at": "2023-10-09T08:21:34.000000Z","updated_at": "2023-10-09T08:22:25.000000Z"}]}
     * 
     */
    // public function index()
    // {
    //     //
    //     // $venues = Venue::all();
    //     $venues = Venue::paginate(8);
    //     if($venues -> count() >0){
    //         return response()->json([
    //             'status' => 'success',
    //             // 'venues' => $venues,
    //             // 'venues' => [],
    //             'pagination' => [
    //                 'current_page' => $venues->currentPage(),
    //                 'total' => $venues->total(),
    //                 'per_page' => $venues->perPage(),
    //             ]
    //         ]);
    //     }else{
    //         return response()->json([
    //             'message' => 'Venues empty'
    //         ]);
    //     }
    // }
    public function index(Request $request)
{
    //$venues = Venue::all();
    $venues_query = Venue::query();

    // if($request->keyword){
    //     $venues_query->where('name','LIKE','%'.$request->keyword.'%');
    // }
    
    if ($request->keyword) {
        $keyword = $request->keyword;
        $venues_query->where(function ($query) use ($keyword) {
            $query->where('name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('address', 'LIKE', '%' . $keyword . '%');
        });
    }
    
    $venues = $venues_query->paginate(8);

    if ($venues->count() > 0) {
        $transformedVenues = $venues->map(function ($venue) {
            // Format and transform each venue as needed
            return [
                'id' => $venue->id,
                'name' => $venue->name,
                'address' => $venue->address
                // Add other properties and transformations as needed
            ];
        });

        return response()->json([
            'status' => 'success',
            'venues' => $transformedVenues,
            'pagination' => [
                'current_page' => $venues->currentPage(),
                'total' => $venues->total(),
                'per_page' => $venues->perPage(),
            ]
        ]);
    } else {
        return response()->json([
            'message' => 'Venues empty'
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
            'address' => 'required',
        ]);

        if($validated -> fails()){
            return response()->json([
                'message' => $validated->messages()
            ]);
        }else{
            Venue::create($request->all());

            $venues = Venue::all();

            return response()->json([
                'message' => 'Venue added successfully',
                'venue' => $venues
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
        $venue = Venue::find($id);

        if($venue){
            return response()->json([
                'status' => 'success',
                'venue' => $venue
            ]);
        }else{
            return response()->json([
                'message' => 'No venue found'
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
            'address' => 'required',
        ]);

        if($validated -> fails()){
            return response()->json([
                'message' => $validated->messages()
            ]);
        }else{
            $venue = Venue::find($id);
            
            if ($venue) {
                # code...
                $venue->update($request->all());

                return response()->json([
                    'message' => 'Venue updated successfully',
                    'venue' => $venue
                ]);
            }else{
                return response()->json([
                    'message' => 'No venue found'
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
        $venue = Venue::find($id);
        if($venue){
            $venue->delete();
            $venues = Venue::all();
            return response()->json([
                'message' => 'Venues deleted successfully',
                'remaining venues' => $venues
            ]);
        }else{
            return response()->json([
                'message' => 'No venue found'
            ]);
        }
    }

    public function getTotalVenues()
    {
      $totalVenues = Venue::count();

      // Calculate the start and end dates for the last month
      $startDate = Carbon::now()->subMonth()->startOfMonth();
      $endDate = Carbon::now()->subMonth()->endOfMonth();
  
      // Get the total number of events within the last month
      $totalVenuesLastMonth = Venue::whereBetween('created_at', [$startDate, $endDate])->count();
  
      // Get the total number of events for all time
      $totalVenuesAllTime = Venue::count();
  
      // Calculate the increase
      $increase = $totalVenuesAllTime - $totalVenuesLastMonth;
  
      // Calculate the percentage increase
      $percentageIncrease = ($totalVenuesLastMonth > 0) ? round(($increase / $totalVenuesLastMonth) * 100, 2): 0;
  

        return response()->json([
            'total_venues' => $totalVenues,
            'percentage_increase_since_last_month' => $percentageIncrease
        ]);
    }

}
