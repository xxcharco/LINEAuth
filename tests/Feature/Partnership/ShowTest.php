<?php
// tests/Feature/Partnership/ShowTest.php

namespace Tests\Feature\Partnership;

use App\Models\User;
use App\Models\Partnership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_partnership_show_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('partnerships.show'));

        $response->assertStatus(200);
    }

    public function test_user_cannot_view_partnership_without_authentication()
    {
        $response = $this->get(route('partnerships.show'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_see_invitation_button_when_no_active_partnership()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('partnerships.show'));

        $response->assertInertia(fn ($page) => $page
            ->component('Partnership/Show')
            ->where('canInvite', true)
            ->where('partnership', null)
        );
    }

    public function test_user_can_see_partner_info_when_has_active_partnership()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $partnership = Partnership::factory()->create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'matched_at' => now(),
        ]);

        $response = $this->actingAs($user1)
            ->get(route('partnerships.show'));

        $response->assertInertia(fn ($page) => $page
            ->component('Partnership/Show')
            ->where('canInvite', false)
            ->has('partnership')
        );
    }
}