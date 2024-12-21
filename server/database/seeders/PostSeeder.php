<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts_count = 20000;

        for ($i = 0; $i < $posts_count; $i++) {
            $post =  Post::create([
                'title' => Str::random(5),
                'body' => Str::random(40),
                'is_active' => \floor(\rand(0, 1))
            ]);
            createReference("posts_1", 36, $post->id);
        }
    }
}
