<?php

namespace Database\Seeders;

use App\Models\UserPostParticipation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserPostParticipationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserPostParticipation::factory()->create([
            'post_id' => 1,
            'participant_id' => 1,
            'is_completed' => true
        ]);
        UserPostParticipation::factory()->create([
            'post_id' => 4,
            'participant_id' => 1,
            'is_completed' => true
        ]);
        UserPostParticipation::factory()->create([
            'post_id' => 5,
            'participant_id' => 1,
            'is_completed' => true
        ]);
        UserPostParticipation::factory()->create([
            'post_id' => 2,
            'participant_id' => 2,
            'is_completed' => true
        ]);
        UserPostParticipation::factory()->create([
            'post_id' => 3,
            'participant_id' => 2,
            'is_completed' => true
        ]);
    }
}
