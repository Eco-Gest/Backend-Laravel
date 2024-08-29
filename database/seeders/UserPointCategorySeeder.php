<?php

namespace Database\Seeders;

use App\Models\UserPointCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserPointCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserPointCategory::factory()->create([
            'user_id' => 1,
            'category_id' => 1,
            'current_point' => 150,
            'total_point' => 150
        ]);
        UserPointCategory::factory()->create([
            'user_id' => 1,
            'category_id' => 2,
            'current_point' => 10,
            'total_point' => 10
        ]);
        UserPointCategory::factory()->create([
            'user_id' => 2,
            'category_id' => 2,
            'current_point' => 10,
            'total_point' => 10
        ]);
        UserPointCategory::factory()->create([
            'user_id' => 2,
            'category_id' => 7,
            'current_point' => 10,
            'total_point' => 10
        ]);
    }
}
