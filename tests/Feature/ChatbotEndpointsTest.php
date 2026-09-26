<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_receive_a_chatbot_reply_without_application_data(): void
    {
        config([
            'services.groq.key' => 'test-key',
            'services.groq.url' => 'https://api.groq.com/openai/v1/chat/completions',
            'services.groq.model' => 'test-model',
        ]);

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [['message' => ['content' => 'Open the Leave Requests module to submit leave.']]],
            ]),
        ]);

        $response = $this->postJson(route('chatbot.guest'), [
            'message' => 'How do I submit leave?',
        ]);

        $response->assertOk()->assertJson([
            'message' => 'Open the Leave Requests module to submit leave.',
        ]);

        Http::assertSent(function ($request) {
            $payload = $request->data();
            $prompt = json_encode($payload['messages']);

            return $request->hasHeader('Authorization', 'Bearer test-key')
                && $payload['model'] === 'test-model'
                && $payload['temperature'] === 0.2
                && $payload['max_tokens'] === 220
                && str_contains($prompt, 'How do I submit leave?')
                && str_contains($prompt, 'Register an admin account')
                && str_contains($prompt, 'Attendance and Payable Days')
                && str_contains($prompt, 'Keep the answer under 100 words')
                && ! str_contains($prompt, 'Company Test')
                && ! str_contains($prompt, 'payrolls');
        });
    }

    public function test_authenticated_users_can_use_the_chatbot(): void
    {
        config(['services.groq.key' => 'test-key']);
        Http::fake([
            '*' => Http::response([
                'choices' => [['message' => ['content' => 'Use the dashboard navigation to get started.']]],
            ]),
        ]);

        $user = $this->createAdminWithCompany();

        $this->actingAs($user)
            ->postJson(route('chatbot.authenticated'), ['message' => 'Where do I start?'])
            ->assertOk()
            ->assertJsonPath('message', 'Use the dashboard navigation to get started.');
    }

    public function test_chatbot_rejects_oversized_messages(): void
    {
        $this->postJson(route('chatbot.guest'), [
            'message' => str_repeat('a', 2001),
        ])->assertUnprocessable()->assertJsonValidationErrors('message');
    }

    public function test_authenticated_route_requires_login(): void
    {
        $this->postJson(route('chatbot.authenticated'), ['message' => 'Hello'])
            ->assertUnauthorized();
    }
}
