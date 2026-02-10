<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Topic;
use App\Models\GlobalMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser()
    {
        return User::create([
            'full_name' => 'Admin Teszt',
            'email' => 'admin@example.com',
            'password' => Hash::make('Jelszo123'),
            'role' => 'admin',
            'xp' => 0,
            'level' => 1
        ]);
    }

    public function test_stats_returns_counts()
    {
        $this->actingAs($this->adminUser());

        Subject::create(['name' => 'Matematika', 'slug' => 'matematika']);
        Subject::create(['name' => 'Irodalom', 'slug' => 'irodalom']);

        $response = $this->getJson('/api/admin/stats');

        $response->assertStatus(200)
                 ->assertJsonStructure(['users', 'subjects', 'topics']);
    }

    public function test_index_users_returns_sorted_list()
    {
        $this->actingAs($this->adminUser());

        User::create([
            'full_name' => 'Béla Tanuló',
            'email' => 'b@example.com',
            'password' => Hash::make('pass'),
            'role' => 'student'
        ]);

        User::create([
            'full_name' => 'Anna Tanuló',
            'email' => 'a@example.com',
            'password' => Hash::make('pass'),
            'role' => 'student'
        ]);

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(200);
        $this->assertEquals('Admin Teszt', $response->json()[0]['full_name']);
    }

    public function test_verify_password_accepts_correct_password()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $response = $this->postJson('/api/admin/verify-password', [
            'password' => 'Jelszo123'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'OK']);
    }

    public function test_verify_password_rejects_wrong_password()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $response = $this->postJson('/api/admin/verify-password', [
            'password' => 'rossz'
        ]);

        $response->assertStatus(403)
                 ->assertJson(['message' => 'Hibás jelszó']);
    }

    public function test_store_subject_creates_subject()
    {
        $this->actingAs($this->adminUser());

        $response = $this->postJson('/api/admin/subjects', [
            'name' => 'Történelem',
            'description' => 'Ókori civilizációk'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('subjects', ['name' => 'Történelem']);
    }

    public function test_destroy_subject_deletes_subject()
    {
        $this->actingAs($this->adminUser());

        $subject = Subject::create(['name' => 'Nyelvtan', 'slug' => 'nyelvtan']);

        $response = $this->deleteJson("/api/admin/subjects/{$subject->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_store_unit_creates_unit()
    {
        $this->actingAs($this->adminUser());

        $subject = Subject::create(['name' => 'Matematika', 'slug' => 'matematika']);

        $response = $this->postJson("/api/admin/subjects/{$subject->id}/units", [
            'title' => 'Pitagorasz-tétel',
            'order' => 1
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('units', ['title' => 'Pitagorasz-tétel']);
    }

    public function test_store_topic_creates_topic()
    {
        $this->actingAs($this->adminUser());

        $subject = Subject::create(['name' => 'Irodalom', 'slug' => 'irodalom']);
        $unit = Unit::create(['title' => 'Arany János', 'order' => 1, 'subject_id' => $subject->id]);

        $response = $this->postJson("/api/admin/units/{$unit->id}/topics", [
            'title' => 'Toldi',
            'type' => 'lesson',
            'xp' => 10,
            'order' => 1
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('topics', ['title' => 'Toldi']);
    }

    public function test_send_system_message_creates_message()
    {
        $this->actingAs($this->adminUser());

        $response = $this->postJson('/api/admin/system-message', [
            'title' => 'Figyelem!',
            'message' => 'Holnap karbantartás lesz.',
            'type' => 'warning'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('global_messages', [
            'title' => 'Figyelem!',
            'is_active' => true
        ]);
    }
}
