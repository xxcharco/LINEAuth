<?php
// tests/Unit/PartnershipServiceTest.php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Partnership;
use App\Services\PartnershipService;
use App\Services\LineMessageService;
use App\Exceptions\PartnershipException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class PartnershipServiceTest extends TestCase
{
    use RefreshDatabase;

    private $lineMessageService;
    private $partnershipService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lineMessageService = Mockery::mock(LineMessageService::class);
        $this->partnershipService = new PartnershipService($this->lineMessageService);
    }

    public function test_can_create_invitation()
    {
        $user = User::factory()->create();

        $partnership = $this->partnershipService->createInvitation($user);

        $this->assertInstanceOf(Partnership::class, $partnership);
        $this->assertEquals($user->id, $partnership->user1_id);
        $this->assertNotNull($partnership->invitation_token);
    }

    public function test_cannot_create_invitation_with_active_partnership()
    {
        $user = User::factory()->create();
        
        // アクティブなパートナーシップを作成
        Partnership::factory()->create([
            'user1_id' => $user->id,
            'user2_id' => User::factory()->create()->id,
            'matched_at' => now()
        ]);

        $this->expectException(PartnershipException::class);
        $this->partnershipService->createInvitation($user);
    }

    public function test_can_process_match()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $partnership = Partnership::factory()->create([
            'user1_id' => $user1->id,
            'invitation_token' => 'test-token',
            'expires_at' => now()->addDays(7)
        ]);

        // LINEメッセージ送信のモック
        $this->lineMessageService
            ->shouldReceive('sendMatchComplete')
            ->twice()
            ->andReturn(true);

        $matchedPartnership = $this->partnershipService->processMatch('test-token', $user2);

        $this->assertEquals($user2->id, $matchedPartnership->user2_id);
        $this->assertNotNull($matchedPartnership->matched_at);
        $this->assertNull($matchedPartnership->invitation_token);
    }

    public function test_cannot_match_expired_invitation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $partnership = Partnership::factory()->create([
            'user1_id' => $user1->id,
            'invitation_token' => 'test-token',
            'expires_at' => now()->subDay()
        ]);

        $this->expectException(PartnershipException::class);
        $this->partnershipService->processMatch('test-token', $user2);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}