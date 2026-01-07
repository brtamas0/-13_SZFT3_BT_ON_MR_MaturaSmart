<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AxelControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_error_when_groq_api_key_is_missing()
    {
        config(['services.groq.key' => null]);

        $response = $this->postJson('/api/ask-axel', [
            'message' => 'Mi a pitagorasz-tétel?',
        ]);

        $response->assertStatus(500)
                 ->assertJson([
                     'error' => 'Hiányzik a GROQ_API_KEY!'
                 ]);
    }

    /** @test */
    public function it_successfully_forwards_request_to_groq_and_returns_answer()
    {
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'A pitagorasz-tétel szerint egy derékszögű háromszögben az átfogó négyzete egyenlő a befogók négyzetének összegével: **a² + b² = c²** 📐'
                        ]
                    ]
                ]
            ], 200)
        ]);

        config(['services.groq.key' => 'fake-key']);

        $response = $this->postJson('/api/ask-axel', [
            'message' => 'Mi a pitagorasz-tétel?',
            'subject' => 'Matematika',
            'topic' => 'Geometria',
            'notes' => 'Derékszögű háromszögek tulajdonságai',
            'history' => [
                ['role' => 'user', 'content' => 'Hello Axel!'],
                ['role' => 'assistant', 'content' => 'Szia! Miben segíthetek? 😊']
            ]
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['answer'])
                 ->assertJson([
                     'answer' => 'A pitagorasz-tétel szerint egy derékszögű háromszögben az átfogó négyzete egyenlő a befogók négyzetének összegével: **a² + b² = c²** 📐'
                 ]);
    }

    /** @test */
    public function it_handles_groq_api_error_gracefully()
    {
        Http::fake([
            'api.groq.com/*' => Http::response(['error' => 'Rate limited'], 429)
        ]);

        config(['services.groq.key' => 'fake-key']);

        $response = $this->postJson('/api/ask-axel', [
            'message' => 'Segíts egy matek feladatban!'
        ]);

        $response->assertStatus(500)
                 ->assertJsonPath('error', 'Groq API Hiba');
    }

    /** @test */
    public function it_validates_required_message_field()
    {
        config(['services.groq.key' => 'fake-key']);

        $response = $this->postJson('/api/ask-axel', [
            // message hiányzik
            'subject' => 'Matematika'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['message']);
    }

    /** @test */
    public function it_uses_default_values_when_optional_fields_are_missing()
    {
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [['message' => ['content' => 'Általános válasz']]]
            ], 200)
        ]);

        config(['services.groq.key' => 'fake-key']);

        $response = $this->postJson('/api/ask-axel', [
            'message' => 'Csak egy általános kérdés'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['answer' => 'Általános válasz']);

        Http::assertSent(function ($request) {
            $payload = $request->data();
            $systemMessage = $payload['messages'][0]['content'];

            return str_contains($systemMessage, 'Tantárgy: Általános') &&
                   str_contains($systemMessage, 'Témakör: Általános') &&
                   str_contains($systemMessage, 'Nincs megadva konkrét tananyag.');
        });
    }
}