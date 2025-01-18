<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConditionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_record_condition()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/conditions/store', [
                'is_high' => true,
                'condition' => '良い',
            ]);

        $response->assertRedirect(route('conditions.graph'));
        $this->assertDatabaseHas('conditions', [
            'is_high' => true,
            'condition' => '良い',
        ]);
    }
}