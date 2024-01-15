<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use Carbon\Carbon;

class CategoryController extends Controller
{
    //
    /**
     * All Category
     * 
     * Shows all venues
     * @response 200 {"status": "success","posts": [{"id": 4,"user_id": 10,"text": "Good Evening!","created_at": "2023-10-09T08:21:34.000000Z","updated_at": "2023-10-09T08:22:25.000000Z"}]}
     * 
     */
    public function index(Request $request)
    {
        //
        // $categories = Category::all();
        
        $categories_query =  Category::query();
         
        if($request->keyword){
            $categories_query->where('name','LIKE','%'.$request->keyword.'%');
        }
        $categories = $categories_query->paginate(8);

        if($categories -> count() >0){
            // return response()->json([
            //     'status' => 'success',
            //     'categories' => $categories
            // ]);
            $transformedCategories = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name
                ];
            });
            return response()->json([
                'status' => 'success',
                'categories' => $transformedCategories,
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'total' => $categories->total(),
                    'per_page' => $categories->perPage(),
                ]
            ]);
        }else{
            return response()->json([
                'message' => 'Categories empty'
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
            'name' => 'required'
        ]);

        if($validated -> fails()){
            return response()->json([
                'message' => $validated->messages()
            ]);
        }else{
            Category::create($request->all());

            $categories = Category::all();

            return response()->json([
                'message' => 'Category added successfully',
                'Category' => $categories
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
        $category = Category::find($id);

        if($category){
            return response()->json([
                'status' => 'success',
                'category' => $category
            ]);
        }else{
            return response()->json([
                'message' => 'No category found'
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
            'name' => 'required'
        ]);

        if($validated -> fails()){
            return response()->json([
                'message' => $validated->messages()
            ]);
        }else{
            $category = Category::find($id);
            
            if ($category) {
                # code...
                $category->update($request->all());

                return response()->json([
                    'message' => 'category updated successfully',
                    'category' => $category
                ]);
            }else{
                return response()->json([
                    'message' => 'No category found'
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
        $category = Category::find($id);
        if($category){
            $category->delete();
            $categories = Category::all();
            return response()->json([
                'message' => 'category deleted successfully',
                'remaining categories' => $categories
            ]);
        }else{
            return response()->json([
                'message' => 'No category found'
            ]);
        }
    }

    public function getTotalCategories()
    {
      $totalCategories = Category::count();

      // Calculate the start and end dates for the last month
      $startDate = Carbon::now()->subMonth()->startOfMonth();
      $endDate = Carbon::now()->subMonth()->endOfMonth();
  
      // Get the total number of events within the last month
      $totalCategoriesLastMonth = Category::whereBetween('created_at', [$startDate, $endDate])->count();
  
      // Get the total number of events for all time
      $totalCategoriesAllTime = Category::count();
  
      // Calculate the increase
      $increase = $totalCategoriesAllTime - $totalCategoriesLastMonth;
  
      // Calculate the percentage increase
      $percentageIncrease = ($totalCategoriesLastMonth > 0) ? round(($increase / $totalCategoriesLastMonth) * 100, 2): 0;
  

        return response()->json([
            'total_categories' => $totalCategories,
            'percentage_increase_since_last_month' => $percentageIncrease
        ]);
    }

}
