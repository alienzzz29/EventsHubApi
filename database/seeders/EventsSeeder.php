<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $events = [];

        for ($i = 1; $i <= 50; $i++) {
            $events[] = [
                'name' => 'Event ' . $i,
                'description' => 'Description for Event ' . $i,
                'date_sched_start' => Carbon::now()->addDays($i),
                'date_sched_end' => Carbon::now()->addDays($i + 2),
                'date_reg_deadline' => Carbon::now()->subDays($i),
                'est_attendants' => rand(500, 2000),
                'location' => 'Location ' . $i,
                'category_id' => rand(1, 2), // Assuming category IDs range from 1 to 5
                'venue_id' => rand(1, 2), // Assuming venue IDs range from 1 to 10
                'is_enabled' => true,
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // Insert data into the events table
        DB::table('events')->insert($events);
    }
}
