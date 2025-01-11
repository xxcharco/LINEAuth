<?php
// tests/Feature/Partnership/InvitationTest.php

namespace Tests\Feature\Partnership;

use App\Models\User;
use App\Models\Partnership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitation_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('partnerships.invite'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Partnership/Confirm')
        );
    }

    public function test_user_can_create_invitation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('partnerships.create'), [
                'agreed_to_terms' => true
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('partnerships', [
            'user1_id' => $user->id,
        ]);
    }

    public function test_user_cannot_create_multiple_invitations()
    {
        $user = User::factory()->create();
        
        // 最初の招待を作成
        Partnership::factory()->create([
            'user1_id' => $user->id,
            'expires_at' => now()->addDays(7)
        ]);

        // 2つ目の招待を試みる
        $response = $this->actingAs($user)
            ->post(route('partnerships.create'), [
                'agreed_to_terms' => true
            ]);

        $response->assertSessionHas('error');
    }

    public function test_invitation_requires_terms_agreement()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('partnerships.create'), [
                'agreed_to_terms' => false
            ]);

        $response->assertSessionHasErrors('agreed_to_terms');
    }
}