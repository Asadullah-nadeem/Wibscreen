<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TerminalToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TerminalSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_generate_terminal_token(): void
    {
        $response = $this->getJson('/terminal-token');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_generate_terminal_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/terminal-token');
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);

        $tokenVal = $response->json('token');
        $this->assertDatabaseHas('terminal_tokens', [
            'user_id' => $user->id,
            'token' => $tokenVal,
        ]);
    }

    public function test_terminal_auth_endpoint_requires_valid_token(): void
    {
        // 1. Missing token
        $response = $this->get('/terminal-auth-backend');
        $response->assertStatus(401);

        // 2. Invalid token
        $response = $this->get('/terminal-auth-backend?token=invalidtoken1234');
        $response->assertStatus(401);

        // 3. Valid token
        $user = User::factory()->create();
        $tokenStr = 'abc123xyz7890123';
        TerminalToken::create([
            'user_id' => $user->id,
            'token' => $tokenStr,
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->get("/terminal-auth-backend?token={$tokenStr}");
        $response->assertStatus(200);
        $response->assertSee('OK');
    }

    public function test_expired_token_is_unauthorized(): void
    {
        $user = User::factory()->create();
        $tokenStr = 'abc123xyz7890123';
        TerminalToken::create([
            'user_id' => $user->id,
            'token' => $tokenStr,
            'expires_at' => now()->subMinutes(1), // Already expired
        ]);

        $response = $this->get("/terminal-auth-backend?token={$tokenStr}");
        $response->assertStatus(401);
    }

    public function test_terminal_auth_endpoint_allows_active_session_without_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/terminal-auth-backend');
        $response->assertStatus(200);
        $response->assertSee('OK');
    }
}
