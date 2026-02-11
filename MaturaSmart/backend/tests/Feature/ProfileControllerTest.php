<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Subject; 
use App\Models\Topic; 
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    private function studentUser($overrides = [])
    {
        return User::create(array_merge([
            'full_name' => 'Profil Tesztelő',
            'email' => 'profile@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'xp' => 0,
            'level' => 1
        ], $overrides));
    }

    public function test_show_returns_profile_with_calculated_stats()
    {
        $user = $this->studentUser(['level' => 2, 'xp' => 50]);

        $subject = Subject::create(['name' => 'Tárgy', 'slug' => 'targy']);
        $topic = Topic::create(['title' => 'Téma', 'slug' => 'tema', 'type' => 'lesson', 'subject_id' => $subject->id]);
        
        $q1 = $topic->questions()->create(['content' => 'Q1', 'xp' => 10, 'type' => 'multiple_choice']);
        $q2 = $topic->questions()->create(['content' => 'Q2', 'xp' => 10, 'type' => 'multiple_choice']);
        $q3 = $topic->questions()->create(['content' => 'Q3', 'xp' => 10, 'type' => 'multiple_choice']);

        DB::table('question_user')->insert([
            ['user_id' => $user->id, 'question_id' => $q1->id, 'is_correct' => true, 'updated_at' => now()],
            ['user_id' => $user->id, 'question_id' => $q2->id, 'is_correct' => true, 'updated_at' => now()], 
            ['user_id' => $user->id, 'question_id' => $q3->id, 'is_correct' => false, 'updated_at' => now()],
        ]);

        $response = $this->actingAs($user)->getJson('/api/profile');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user' => ['id', 'full_name', 'email', 'level', 'xp'],
                     'stats' => ['next_level_xp', 'level_progress', 'completed_topics']
                 ]);

        $stats = $response->json('stats');
        $this->assertEquals(200, $stats['next_level_xp']); 
        $this->assertEquals(25, $stats['level_progress']);
        $this->assertEquals(2, $stats['completed_topics']); 
    }

    public function test_password_update_succeeds_with_correct_credentials()
    {
        $user = $this->studentUser(['password' => Hash::make('RegiJelszo123')]);

        $payload = [
            'current_password' => 'RegiJelszo123',
            'new_password' => 'UjSzuperJelszo!!!',
            'new_password_confirmation' => 'UjSzuperJelszo!!!'
        ];

        $response = $this->actingAs($user)->putJson('/api/profile/password', $payload);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Jelszó sikeresen megváltoztatva!']);

        $user->refresh();
        $this->assertTrue(Hash::check('UjSzuperJelszo!!!', $user->password));
    }

    public function test_password_update_fails_if_current_password_is_wrong()
    {
        $user = $this->studentUser(['password' => Hash::make('ValodiJelszo')]);
        $payload = [
            'current_password' => 'RosszJelszo',
            'new_password' => 'UjJelszo123',
            'new_password_confirmation' => 'UjJelszo123'
        ];
        $response = $this->actingAs($user)->putJson('/api/profile/password', $payload);
        $response->assertStatus(422)->assertJson(['message' => 'A jelenlegi jelszó hibás.']);
    }

    public function test_password_update_validates_new_password_rules()
    {
        $user = $this->studentUser();
        $payload = ['current_password' => 'password123', 'new_password' => 'rovid', 'new_password_confirmation' => 'rovid'];
        $response = $this->actingAs($user)->putJson('/api/profile/password', $payload);
        $response->assertStatus(422)->assertJsonValidationErrors(['new_password']);
    }

    public function test_password_update_validates_confirmation_match()
    {
        $user = $this->studentUser();
        $payload = ['current_password' => 'password123', 'new_password' => 'UjJelszo123', 'new_password_confirmation' => 'MasikJelszo'];
        $response = $this->actingAs($user)->putJson('/api/profile/password', $payload);
        $response->assertStatus(422)->assertJsonValidationErrors(['new_password']);
    }
}