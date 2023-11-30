<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //
    public function index(){
        return User::all();
    }

    
    public function register(Request $request){
        $role = Role::where('id', 3)->get();

        $fields = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'contact_no' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'contact_no' => $fields['contact_no'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ])->assignRole($role);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $request->email)->first();

        // Check password
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Invalid input'
            ], 401);
        }

        if($user) {
            // $user->is_active = 1;
            $user->save();
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        // $token = $user->createAuthToken('myapptoken',20)->plainTextToken;

        $response = [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'contact_no' => $user->contact_no,
                'email' => $user->email,
                'roles' => $user->roles->first(), // Include the role names in the response
            ],
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        $user = auth()->user();
        if($user) {
            // $user->is_active = 0;
            $user->save();
        }
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged Out'
        ];
    }

    public function search($name)
    {
        return User::where('name', 'like','%'.$name.'%')->get();
    }

    // public function eventsAttended(string $id)
    // {

    //     // return User::find($id)->eventAttendees;
    //     $userByID = User::find($id)->eventAttendees->with('event')->get();
    //     return response()->json([
    //         'message' => 'Showing all events',
    //         'user' =>  $userByID
    //     ]);
    // }
    
    // public function eventsAttended(string $id)
    // {
    //     $userEvents = User::with(['eventAttendees.event'])->find($id);
        
    //     return response()->json([
    //         'message' => 'Showing all events by user',
    //         'user_events' =>  $userEvents->eventAttendees
    //     ]);
    // }
}
