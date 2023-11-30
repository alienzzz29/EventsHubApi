<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use App\Models\User;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(WithFaker::class);

it('test_login_api', function(){
    $user = User::factory()->create(['password' => bcrypt('foo')]);
    $formData = [
        'email'=> $user->email,
        'password'=> 'foo'
    ];

    actingAs($user)
        ->post('/api/login',$formData)
        ->assertStatus(201);
   
    
});

    

it('test_register_api', function(){
    $password = $this->faker->password();

    $formData = [
        'first_name'=> $this->faker->name(),
        'last_name'=> $this->faker->name(),
        'contact_no'=> '1000',
        'email'=> $this->faker->email(),
        'password'=> $password,
        'password_confirmation'=> $password
    ];

    $this->post('/api/register',$formData)
    ->assertStatus(201);
});

it('test_logout_api', function(){
    $user = User::factory()->create();

    $this->actingAs($user)->post('/api/logout')
    ->assertStatus(200);
});


// it('test_show_user_api', function (){
//     actingAs(User::factory()->create())
//     ->get('/api/users/')
//     ->assertOk();

// });


// it('test_show_user_by_id_api', function()
// {

//     $id = '1';

//     actingAs(User::factory()->create())
//     ->get('/api/user/{id}')
//     ->assertOk();
// });

// it('test_create_user_api', function()
// {
    
//     $user = User::factory()->create();

//     $formData = [
//         'name'=> $this->faker->word(),
//         'description'=> $this->faker->sentence(),
//         'date'=> $this->faker->date(),
//         'location'=> $this->faker->sentence(),
//         'category_id'=> $this->faker->randomDigit(),
//         'venue_id'=> $this->faker->randomDigit(),
//         'user_id'=> $this->faker->randomDigit(),
//     ];

//     actingAs($user)
//     ->post('/api/users', $formData)
//     ->assertOk();
// });

// it('test_update_user_api', function()
// {
//     $user = User::factory()->create();

//     $id = '1';

//     actingAs($user)
//     ->post('/api/user/{$id}/update')
//     ->assertOk();
// });

// it('test_delete_user_api',function()
// {
//     $user = User::factory()->create();

//     $id = '1';

//     actingAs($user)
//     ->delete('/api/user/{$id}/delete')
//     ->assertOk();
// });