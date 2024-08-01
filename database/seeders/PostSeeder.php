<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::factory()
        ->create([
            'category_id' => 1,
            'author_id' => 1,
            'title' => 'Semaine de vélo taf',
            'description' => 'Semaine de vélo taf',
            'type' => 'action',
            'level' => 'medium'
        ]);
        Post::factory()
        ->create([
            'category_id' => 2,
            'author_id' => 2,
            'title' => "Acheter de l'alcool local",
            'description' => "Acheter de l'alcool local pour le consommmer AVEC ou SANS mon voisin",
            'type' => 'action',
            'level' => 'easy'
        ]);
        Post::factory()
        ->create([
            'category_id' => 7,
            'author_id' => 2,
            'title' => 'Spotify en mode hors-ligne',
            'description' => 'Spotify en mode hors-ligne pour acheter moins de vinyle !',
            'type' => 'action',
            'level' => 'easy'
        ]);
        Post::factory()
        ->create([
            'category_id' => 7,
            'author_id' => 1,
            'title' => 'Recycler mon PC en le passant sous linux',
            'description' => 'Recycler mon PC en le passant sous linux',
            'type' => 'action',
            'level' => 'easy'
        ]);
        Post::factory()
        ->create([
            'category_id' => 1,
            'author_id' => 1,
            'title' => 'Velo taf',
            'description' => 'Velo taf pendant 5 jours',
            'type' => 'challenge',
            'level' => 'medium',
            "start_date" => "2023-11-23T00:00:00.000",
            "end_date" => "2023-11-28T00:00:00.000"
        ]);
    }
}
