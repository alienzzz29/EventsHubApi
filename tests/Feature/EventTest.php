<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use App\Models\User;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(WithFaker::class);


it('test_show_event_api', function (){
    actingAs(User::factory()->create())
    ->get('/api/events/')
    ->assertOk();

});


it('test_show_event_by_id_api', function()
{

    $id = '1';

    actingAs(User::factory()->create())
    ->get('/api/event/{id}')
    ->assertOk();
});

it('test_create_event_api', function()
{
    
    $user = User::factory()->create();

    $formData = [
        'name'=> $this->faker->word(),
        'description'=> $this->faker->sentence(),
        'date'=> $this->faker->date(),
        'location'=> $this->faker->sentence(),
        'category_id'=> $this->faker->randomDigit(),
        'venue_id'=> $this->faker->randomDigit(),
        'user_id'=> $this->faker->randomDigit(),
    ];

    actingAs($user)
    ->post('/api/events', $formData)
    ->assertOk();
});

it('test_update_event_api', function()
{
    $user = User::factory()->create();

    $id = '1';

    actingAs($user)
    ->post('/api/event/{$id}/update')
    ->assertOk();
});

it('test_delete_event_api',function()
{
    $user = User::factory()->create();

    $id = '1';

    actingAs($user)
    ->delete('/api/event/{$id}/delete')
    ->assertOk();
});