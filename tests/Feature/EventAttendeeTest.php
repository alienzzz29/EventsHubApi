<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use App\Models\User;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(WithFaker::class);


it('test_show_event_attendee_api', function (){
    actingAs(User::factory()->create())
    ->get('/api/event_attendees/')
    ->assertOk();

});


it('test_show_event_attendee_by_id_api', function()
{

    $id = '1';

    actingAs(User::factory()->create())
    ->get('/api/event_attendee/{id}')
    ->assertOk();
});

it('test_create_event_attendee_api', function()
{
    
    $user = User::factory()->create();

    $formData = [
        'user_id'=> $this->faker->randomDigit(),
        'event_id'=> $this->faker->randomDigit(),
    ];

    actingAs($user)
    ->post('/api/event_attendees', $formData)
    ->assertOk();
});

it('test_update_event_attendee_api', function()
{
    $user = User::factory()->create();

    $id = '1';

    actingAs($user)
    ->post('/api/event_attendee/{$id}/update')
    ->assertOk();
});

it('test_delete_event_attendee_api',function()
{
    $user = User::factory()->create();

    $id = '1';

    actingAs($user)
    ->delete('/api/event_attendee/{$id}/delete')
    ->assertOk();
});