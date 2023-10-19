<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use App\Models\User;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(WithFaker::class);


it('test_show_category_api', function (){
    actingAs(User::factory()->create())
    ->get('/api/categories/')
    ->assertOk();

});


it('test_show_category_by_id_api', function()
{

    $id = '1';

    actingAs(User::factory()->create())
    ->get('/api/category/{id}')
    ->assertOk();
});

it('test_create_category_api', function()
{
    
    $user = User::factory()->create();

    $formData = [
        'name'=> $this->faker->word(),
    ];

    actingAs($user)
    ->post('/api/categories', $formData)
    ->assertOk();
});

it('test_update_category_api', function()
{
    $user = User::factory()->create();

    $id = '1';

    actingAs($user)
    ->post('/api/category/{$id}/update')
    ->assertOk();
});

it('test_delete_category_api',function()
{
    $user = User::factory()->create();

    $id = '1';

    actingAs($user)
    ->delete('/api/category/{$id}/delete')
    ->assertOk();
});