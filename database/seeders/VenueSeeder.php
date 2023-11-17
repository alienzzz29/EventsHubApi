<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $venues = [
            [
                'name' => 'Venue A',
                'address' => 'Address A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Venue B',
                'address' => 'Address B',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Add more venues as needed
        ];

        // Insert data into the venues table
        foreach ($venues as $venue) {
            DB::table('venues')->insert($venue);
        }
    }
}
