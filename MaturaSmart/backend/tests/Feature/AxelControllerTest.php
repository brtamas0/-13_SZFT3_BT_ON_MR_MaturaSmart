<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AxelControllerTest extends TestCase
{
    use RefreshDatabase;

    private function studentUser()
    {
        return User::create([
            'full_name' => 'Axel Teszt',
            'email' => 'axel.test@example.com',
            'password' => Hash::make('password'), 
            'role' => 'student',
            'xp' => 100,
            'level' => 2
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        putenv('GROQ_API_KEY=test_api_key');
    }

    public function test_ask_returns_answer_successfully()
    {
        $user = $this->studentUser();
        
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Szia! Axel vagyok.'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)->postJson('/api/ask-axel', [
            'message' => 'Mi az a Pitagorasz-tétel?',
            'subject' => 'Matematika',
            'topic' => 'Geometria',
            'notes' => 'a^2 + b^2 = c^2',
            'history' => []
        ]);
        $response->assertStatus(200)
                 ->assertJson([
                     'answer' => 'Szia! Axel vagyok.'
                 ]);
    }

    public function test_ask__api_error()
    {
        $user = $this->studentUser();
        Http::fake([
            'api.groq.com/*' => Http::response(['error' => 'Groq is down'], 500)
        ]);

        $response = $this->actingAs($user)->postJson('/api/ask-axel', [
            'message' => 'Segítség!',
        ]);

        $response->assertStatus(500)
                 ->assertJson([
                     'error' => 'Hiba az AI szolgáltatásban.'
                 ]);
    }


    public function test_ask_trims_long_notes()
    {
        $user = $this->studentUser();
        
        Http::fake();
        $longNotes = str_repeat('a', 10000);

        $this->actingAs($user)->postJson('/api/ask-axel', [
            'message' => 'Teszt',
            'notes' => $longNotes
        ]);
        Http::assertSent(function ($request) {
            $sentContent = $request['messages'][0]['content'];
            
            return substr_count($sentContent, 'aaaaaaaaaa') > 0 && strlen($sentContent) < 11000;
        });
    }
}