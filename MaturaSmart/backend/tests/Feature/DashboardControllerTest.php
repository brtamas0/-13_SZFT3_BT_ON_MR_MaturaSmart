<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Topic;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private function studentUser()
    {
        return User::create([
            'full_name' => 'Teszt Diák',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'xp' => 0,
            'level' => 1
        ]);
    }

    public function test_index_base_structure()
    {
        $user = $this->studentUser();

        $response = $this->actingAs($user)->getJson('/api/dashboard');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user',
                     'last_topic',
                     'subjects',
                     'quote'
                 ]);
        $this->assertNull($response->json('last_topic'));
    }

    public function test_index_subject_list()
    {
        $user = $this->studentUser();

        Subject::create(['name' => 'Matematika', 'slug' => 'matematika']);
        Subject::create(['name' => 'Történelem', 'slug' => 'tortenelem']);

        $response = $this->actingAs($user)->getJson('/api/dashboard');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('subjects'));
        $this->assertEquals('Matematika', $response->json('subjects')[0]['title']);
    }


    public function test_subject_colors()
    {
        $user = $this->studentUser();

        for ($i = 1; $i <= 9; $i++) {
            Subject::create(['name' => "Subject $i", 'slug' => "subj-$i"]);
        }

        $response = $this->actingAs($user)->getJson('/api/dashboard');
        $subjects = $response->json('subjects');

        foreach ($subjects as $subj) {
            $this->assertNotNull($subj['visuals']['bg']);
            $this->assertNotNull($subj['visuals']['color']);
        }
    }
}