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
        // return User::all();
        // $users = User::with('roles')->paginate(8);
        $users = User::with('roles')->where('id', '!=', 1)->paginate(8);

        return response()->json([
            'status' => 'success',
            'users' => $users,
            'pagination' => [
                'current_page' => $users->currentPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
            ]
        ]);
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

    public function store(Request $request){
        

        $fields = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'contact_no' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'role_id' => 'required|integer'
        ]);
        $role = Role::where('id', $fields['role_id'])->get();

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'contact_no' => $fields['contact_no'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ])->assignRole($role);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            // 'user' => $user,
            // 'token' => $token
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

    public function update(Request $request, string $id)
    {
        $fields = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'contact_no' => 'required|string',
            'password' => 'sometimes|required|string|confirmed'// Only required when provided
            // 'role_id' => 'required|integer'
        ]);
        
        $user = User::find($id);

        if ($user) {
            // $user->update([
            //     'first_name' => $fields['first_name'],
            //     'last_name' => $fields['last_name'],
            //     'contact_no' => $fields['contact_no'],
            //     'password' => isset($fields['password']) ? bcrypt($fields['password']) : $user->password,
            // ]);

            $user->update($request->all());

            // $role = Role::where('id', $fields['role_id'])->first();
            // if ($role) {
            //     $user->syncRoles([$role->id]);
            // }

            return response()->json([
                'message' => 'User updated successfully',
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'contact_no' => $user->contact_no,
                    'email' => $user->email,
                    'roles' => $user->roles->first(), // Include the role names in the response
                ]
            ]);
        } else {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }
    public function delete(string $id)
    {
        //
        $user = User::find($id);
        if($user){
            $user->delete();
            $users = User::all();
            return response()->json([
                'message' => 'Users deleted successfully',
                'remaining users' => $users
            ]);
        }else{
            return response()->json([
                'message' => 'No user found'
            ]);
        }
    }

    // public function update(Request $request, string $id)
    // {
    //     //
    //     $validated = Validator::make($request->all(),[
    //         'name' => 'required',
    //         'description' => 'required | max:166',
    //         'date_sched_start' => 'required',
    //         'date_sched_end' => 'required',
    //         'date_reg_deadline' => 'required',
    //         'est_attendants' => 'required | integer',
    //         'location' => 'required',
    //         'images' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048', 
    //         'category_id' => 'required|integer',
    //         'venue_id' => 'required|integer',
    //         'event_status' => 'required|integer',
    //         'user_id' => 'required|integer',
    //     ]);

    //     if($validated -> fails()){
    //         return response()->json([
    //             'message' => $validated->messages()
    //         ]);
    //     }else{
    //         $event = Event::find($id);
            
    //         if ($event) {
    //             # code...
    //             $event->update($request->all());

    //             return response()->json([
    //                 'message' => 'event updated successfully',
    //                 'event' => $event
    //             ]);
    //         }else{
    //             return response()->json([
    //                 'message' => 'No event found'
    //             ]); 
    //         }

            
    //     }
    // }
   
}
