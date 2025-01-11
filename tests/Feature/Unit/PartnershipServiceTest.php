<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Partnership;
use App\Services\PartnershipService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PartnershipServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected $partnershipService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->partnershipService = new PartnershipService();
    }

    /** @test */
    public function a_user_can_create_an_invitation()
    {
        $user = User::factory()->create();

        $partnership = $this->partnershipService->createInvitation($user);

        $this->assertEquals($user->id, $partnership->user1_id);
        $this->assertNotNull($partnership->invitation_token);
        $this->assertGreaterThan(now(), $partnership->expires_at);
    }

    /** @test */
    public function it_can_process_a_valid_token_match()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $partnership = $this->partnershipService->createInvitation($user1);

        $updatedPartnership = $this->partnershipService->processMatch($partnership->invitation_token, $user2);

        $this->assertEquals($user2->id, $updatedPartnership->user2_id);
        $this->assertNotNull($updatedPartnership->matched_at);
        $this->assertNull($updatedPartnership->invitation_token);
    }

    // 追加のテストケースをここに記述
}