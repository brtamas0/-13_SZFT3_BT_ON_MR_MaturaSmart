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

class GamificationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function studentUser($overrides = [])
    {
        return User::create(array_merge([
            'full_name' => 'Teszt Diák',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'xp' => 0,
            'level' => 1
        ], $overrides));
    }

    public function test_leaderboard_order_xp()
    {
        $user1 = $this->studentUser(['email' => 'u1@ex.com', 'xp' => 100, 'full_name' => 'Gold User']);
        $user2 = $this->studentUser(['email' => 'u2@ex.com', 'xp' => 500, 'full_name' => 'Diamond User']);
        $user3 = $this->studentUser(['email' => 'u3@ex.com', 'xp' => 50, 'full_name' => 'Silver User']);

        $response = $this->actingAs($user3)->getJson('/api/leaderboard');

        $response->assertStatus(200);
        $leaderboard = $response->json('leaderboard');
        $this->assertEquals('Diamond User', $leaderboard[0]['full_name']);
        $this->assertEquals('Gold User', $leaderboard[1]['full_name']);
        $this->assertEquals('Silver User', $leaderboard[2]['full_name']);
    }



    public function test_leaderboard_rankcalc_correctly()
    {
        User::create(['full_name' => 'Top1', 'email' => '1@e.com', 'password' => 'x', 'xp' => 1000]);
        User::create(['full_name' => 'Top2', 'email' => '2@e.com', 'password' => 'x', 'xp' => 800]);
        $me = $this->studentUser(['xp' => 500]);
        User::create(['full_name' => 'Top4', 'email' => '4@e.com', 'password' => 'x', 'xp' => 100]);

        $response = $this->actingAs($me)->getJson('/api/leaderboard');

        $response->assertStatus(200)
                 ->assertJson([
                     'user_rank' => 3,
                     'user_xp' => 500
                 ]);
    }



    public function test_complete_xp_for_correct_answers()
    {
        $user = $this->studentUser(['xp' => 0]);
        
        $subject = Subject::create(['name' => 'Teszt Tárgy', 'slug' => 'teszt-targy']);

        $topic = Topic::create([
            'title' => 'Teszt Lecke', 
            'slug' => 'teszt-lecke', 
            'type' => 'lesson',
            'subject_id' => $subject->id
        ]);

        $question = $topic->questions()->create(['content' => '2+2?', 'xp' => 10, 'type' => 'multiple_choice']);
        $correctAnswer = $question->answers()->create(['text' => '4', 'is_correct' => true]);
        $question->answers()->create(['text' => '5', 'is_correct' => false]);

        $payload = [
            'topic_id' => $topic->id,
            'answers' => [
                $question->id => $correctAnswer->id
            ]
        ];

        $response = $this->actingAs($user)->postJson('/api/gamification/complete-topic', $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'passed' => true,
                     'xp_gained' => 10,
                     'total_xp' => 10
                 ]);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'xp' => 10, 'last_topic_id' => $topic->id]);
    }



    public function test_complete_no_xp_for_already_completed_topic()
    {
        $user = $this->studentUser(['xp' => 100]);
        
        $subject = Subject::create(['name' => 'Matek', 'slug' => 'matek']);

        $topic = Topic::create([
            'title' => 'Ismétlés', 
            'slug' => 'ism', 
            'type' => 'lesson',
            'subject_id' => $subject->id
        ]);

        $question = $topic->questions()->create([
            'content' => '?', 
            'xp' => 50,
            'type' => 'multiple_choice' 
        ]);
        
        $correctAnswer = $question->answers()->create(['text' => '!', 'is_correct' => true]);

        DB::table('question_user')->insert([
            'user_id' => $user->id,
            'question_id' => $question->id,
            'is_correct' => true
        ]);

        $payload = [
            'topic_id' => $topic->id,
            'answers' => [$question->id => $correctAnswer->id]
        ];

        $response = $this->actingAs($user)->postJson('/api/gamification/complete-topic', $payload);

        $response->assertStatus(200);
        $this->assertEquals(0, $response->json('xp_gained'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'xp' => 100]);
    }



    public function test_complete_fail_if_score_is_low()
    {
        $user = $this->studentUser();
        
        $subject = Subject::create(['name' => 'Fizika', 'slug' => 'fizika']);

        $topic = Topic::create([
            'title' => 'Szigorú Vizsga', 
            'slug' => 'vizsga', 
            'type' => 'test', 
            'passing_percentage' => 50,
            'subject_id' => $subject->id
        ]);

        $q1 = $topic->questions()->create(['content' => 'Q1', 'xp' => 10, 'type' => 'multiple_choice']);
        $a1_wrong = $q1->answers()->create(['text' => 'W', 'is_correct' => false]);

        $q2 = $topic->questions()->create(['content' => 'Q2', 'xp' => 10, 'type' => 'multiple_choice']);
        $a2_wrong = $q2->answers()->create(['text' => 'W', 'is_correct' => false]);
        $payload = [
            'topic_id' => $topic->id,
            'answers' => [
                $q1->id => $a1_wrong->id,
                $q2->id => $a2_wrong->id
            ]
        ];

        $response = $this->actingAs($user)->postJson('/api/gamification/complete-topic', $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'passed' => false,
                     'xp_gained' => 0
                 ]);
    }



    public function test_complete_pass_if_score_is_high_enough()
    {
        $user = $this->studentUser();
        
        $subject = Subject::create(['name' => 'Kémia', 'slug' => 'kemia']);
        $topic = Topic::create([
            'title' => 'Vizsga', 
            'slug' => 'vizsga-ok', 
            'type' => 'test', 
            'passing_percentage' => 50,
            'subject_id' => $subject->id
        ]);

        $q1 = $topic->questions()->create(['content' => 'Q1', 'xp' => 10, 'type' => 'multiple_choice']);
        $a1_correct = $q1->answers()->create(['text' => 'C', 'is_correct' => true]);

        $q2 = $topic->questions()->create(['content' => 'Q2', 'xp' => 10, 'type' => 'multiple_choice']);
        $a2_wrong = $q2->answers()->create(['text' => 'W', 'is_correct' => false]);

        $payload = [
            'topic_id' => $topic->id,
            'answers' => [
                $q1->id => $a1_correct->id,
                $q2->id => $a2_wrong->id 
            ]
        ];

        $response = $this->actingAs($user)->postJson('/api/gamification/complete-topic', $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'passed' => true,
                     'xp_gained' => 10 
                 ]);
    }
}