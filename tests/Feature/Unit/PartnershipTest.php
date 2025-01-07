<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Partnership;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PartnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_partnership()
    {
        $user1 = User::factory()->create();
        $partnership = Partnership::create([
            'user1_id' => $user1->id,
            'invitation_token' => 'test-token',
            'invitation_sent_at' => now(),
            'expires_at' => now()->addDays(7)
        ]);

        $this->assertDatabaseHas('partnerships', [
            'user1_id' => $user1->id,
            'invitation_token' => 'test-token'
        ]);
    }

    public function test_can_match_users()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $partnership = Partnership::create([
            'user1_id' => $user1->id,
            'invitation_token' => 'test-token',
            'invitation_sent_at' => now(),
            'expires_at' => now()->addDays(7)
        ]);

        $partnership->update([
            'user2_id' => $user2->id,
            'matched_at' => now(),
            'invitation_token' => null
        ]);

        $this->assertTrue($partnership->isActive());
        $this->assertEquals($user2->id, $partnership->user2_id);
    }

    public function test_user_can_have_only_one_active_partnership()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $partnership = Partnership::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'matched_at' => now()
        ]);

        $this->assertEquals(
            $partnership->id,
            $user1->activePartnership()->id
        );
    }
}