<?php
// tests/Feature/Partnership/MatchingTest.php

namespace Tests\Feature\Partnership;

use App\Models\User;
use App\Models\Partnership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_join_page_is_displayed_with_valid_token()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $partnership = Partnership::factory()->create([
            'user1_id' => $user1->id,
            'invitation_token' => 'test-token',
            'expires_at' => now()->addDays(7)
        ]);

        $response = $this->actingAs($user2)
            ->get(route('partnerships.join', ['token' => 'test-token']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Partnership/Join')
            ->where('token', 'test-token')
            ->where('canAccept', true)
        );
    }

    public function test_user_can_accept_invitation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $partnership = Partnership::factory()->create([
            'user1_id' => $user1->id,
            'invitation_token' => 'test-token',
            'expires_at' => now()->addDays(7)
        ]);

        $response = $this->actingAs($user2)
            ->post(route('partnerships.match', ['token' => 'test-token']));

        $this->assertDatabaseHas('partnerships', [
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'invitation_token' => null,
        ]);
    }

    public function test_user_cannot_accept_expired_invitation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $partnership = Partnership::factory()->create([
            'user1_id' => $user1->id,
            'invitation_token' => 'test-token',
            'expires_at' => now()->subDay()
        ]);

        $response = $this->actingAs($user2)
            ->post(route('partnerships.match', ['token' => 'test-token']));

        $response->assertSessionHas('error');
    }

    public function test_user_cannot_accept_own_invitation()
    {
        $user = User::factory()->create();

        $partnership = Partnership::factory()->create([
            'user1_id' => $user->id,
            'invitation_token' => 'test-token',
            'expires_at' => now()->addDays(7)
        ]);

        $response = $this->actingAs($user)
            ->post(route('partnerships.match', ['token' => 'test-token']));

        $response->assertSessionHas('error');
    }
}