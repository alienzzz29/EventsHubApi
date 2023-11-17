<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Category A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Category B',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Add more categories as needed
        ];

        // Insert data into the categories table
        foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }
    }
}
