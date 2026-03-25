<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_validates_required_fields()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_refuses_invalid_credentials()
    {
        User::create([
            'full_name' => 'Kis Ádám',
            'email' => 'kisadam@example.com',
            'password' => Hash::make('HelyesJelszo123'),
            'xp' => 0,
            'level' => 1,
            'role' => 'student'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'kisadam@example.com',
            'password' => 'RosszJelszo',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Hibás email cím vagy jelszó!']);
    }

    public function test_login_accepts_valid_credentials()
    {
        User::create([
            'full_name' => 'Kis Ádám',
            'email' => 'kisadam@example.com',
            'password' => Hash::make('Jelszo123'),
            'xp' => 0,
            'level' => 1,
            'role' => 'student'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'kisadam@example.com',
            'password' => 'Jelszo123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'token', 'user']);
    }

    public function test_register_validates_required_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['full_name', 'email', 'password']);
    }

    public function test_register_refuses_invalid_name()
    {
        $response = $this->postJson('/api/register', [
            'full_name' => 'Teszt123',
            'email' => 'rossz@example.com',
            'password' => 'Jelszo123',
            'password_confirmation' => 'Jelszo123',
        ]);

        $response->assertStatus(422);
    }

    public function test_register_creates_user()
    {
        $response = $this->postJson('/api/register', [
            'full_name' => 'Kis Ádám',
            'email' => 'uj@example.com',
            'password' => 'Jelszo123',
            'password_confirmation' => 'Jelszo123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'uj@example.com']);
    }
}
