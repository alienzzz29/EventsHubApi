<?php

use App\Http\Controllers\VenueController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventAttendeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/login', [UserController::class, 'login']); // Login User
Route::post('/register', [UserController::class, 'register']); // Register User

//Public Routes
Route::get('/events', [EventController::class, 'index']);
Route::get('/event/{id}', [EventController::class, 'show']);

Route::get('/venues', [VenueController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/roles', [RoleController::class, 'index']);

Route::group(['middleware'=>['auth:sanctum']],function(){
    // Venue
    // Route::get('/venues', [VenueController::class, 'index']);
    Route::post('/venues', [VenueController::class, 'store']);
    Route::get('/venue/{id}', [VenueController::class, 'show']);
    Route::post('/venue/{id}/update', [VenueController::class, 'update']);
    Route::delete('/venue/{id}/delete', [VenueController::class, 'delete']);
    // Category
    // Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::post('/category/{id}/update', [CategoryController::class, 'update']);
    Route::delete('/category/{id}/delete', [CategoryController::class, 'delete']);
    // Event
    // Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/event/{id}/auth', [EventController::class, 'showByIdAuth']);
    Route::get('/event/{id}/merchant', [EventController::class, 'showAllByUserId']);
    Route::get('/event/{user_id}/merchant/{event_status}', [EventController::class, 'showAllByUserIdAndStatus']);
    Route::post('/event/{id}/update', [EventController::class, 'update']);
    Route::post('/event/{id}/update_status', [EventController::class, 'updateStatus']);
    Route::delete('/event/{id}/delete', [EventController::class, 'delete']);
    Route::get('/events/admin', [EventController::class, 'indexAdmin']);
    // User
    Route::get('/users', [UserController::class, 'index']); // Show Users
    Route::post('/users', [UserController::class, 'store']);
    // Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users/{id}/update', [UserController::class, 'update']);
    Route::delete('/users/{id}/delete', [UserController::class, 'delete']);

    Route::get('/users/search/{name}', [UserController::class, 'search']); // Search User
    // Route::get('/users/events_attended/{id}', [UserController::class, 'eventsAttended']); // Search User
    // Event Attendeee
    Route::get('/event_attendees', [EventAttendeeController::class, 'index']);
    Route::post('/event_attendees', [EventAttendeeController::class, 'store']);
    Route::post('/event_attendees/bulk', [EventAttendeeController::class, 'store']);
    Route::get('/event_attendee/{id}', [EventAttendeeController::class, 'show']);
    Route::post('/event_attendee/{id}/update', [EventAttendeeController::class, 'update']);
    Route::delete('/event_attendee/event/{event_id}/user/{user_id}/delete', [EventAttendeeController::class, 'delete']);
    Route::get('/event_attendee/user/{user_id}', [EventAttendeeController::class, 'getByUserId']);
});


Route::post('/logout', [UserController::class, 'logout']); // User logged out
