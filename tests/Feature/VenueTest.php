<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use App\Models\User;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(WithFaker::class);


it('test_show_venue_api', function (){
    actingAs(User::factory()->create())
    ->get('/api/venues/')
    ->assertOk();

});


it('test_show_venue_by_id_api', function()
{

    $id = '1';

    actingAs(User::factory()->create())
    ->get('/api/venue/{id}')
    ->assertOk();
});

it('test_create_venue_api', function()
{
    
    $user = User::factory()->create();

    $formData = [
        'name'=> $this->faker->word(),
        'address'=> $this->faker->sentence(),
    ];

    actingAs($user)
    ->post('/api/venues', $formData)
    ->assertOk();
});

it('test_update_venue_api', function()
{
    $user = User::factory()->create();

    $id = '1';

    actingAs($user)
    ->post('/api/venue/{$id}/update')
    ->assertOk();
});

it('test_delete_venue_api',function()
{
    $user = User::factory()->create();

    $id = '1';

    actingAs($user)
    ->delete('/api/venue/{$id}/delete')
    ->assertOk();
});