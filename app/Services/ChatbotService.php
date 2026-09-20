<?php

namespace App\Services;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ChatbotService
{
    public function __construct(private readonly HttpFactory $http) {}

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function reply(string $message, array $history = []): string
    {
        $apiKey = config('services.groq.key');
        $baseUrl = config('services.groq.url', 'https://api.groq.com/openai/v1/chat/completions');
        $model = config('services.groq.model', 'openai/gpt-oss-120b');
        $systemPrompt = config('services.groq.system_prompt', 'You are a helpful assistant.');
        $guidePath = base_path('resources/docs/product-guide.md');

        if (empty($apiKey)) {
            throw new RuntimeException('The Groq API key is not configured in services.groq.key.');
        }

        $guide = is_file($guidePath)
            ? file_get_contents($guidePath)
            : 'The product guide is unavailable. Do not invent application workflows.';
        $guide = mb_substr((string) $guide, 0, 18000);

        $messages = [];

        // Only include system prompt if it contains actual content
        if (! empty(trim((string) $systemPrompt))) {
            $messages[] = [
                'role' => 'system',
                'content' => trim((string) $systemPrompt)."\n\nResponse rules:\n- Answer the exact question first in plain, friendly language.\n- Keep the answer under 100 words unless the user asks for more detail.\n- Use at most 4 short numbered steps or bullets for procedures.\n- Do not repeat the question, add greetings, or include unnecessary background.\n- If the guide does not support the requested action, say that clearly and suggest the closest available next step.\n- Never invent fields, permissions, calculations, or workflows.\n\nUse only this verified product guide for application workflow questions. Do not claim to have performed actions or accessed private records.\n\n--- PRODUCT GUIDE ---\n".$guide,
            ];
        }

        // Append past context history
        foreach (array_slice($history, -8) as $item) {
            if (isset($item['role'], $item['content']) && in_array($item['role'], ['user', 'assistant'], true)) {
                $messages[] = [
                    'role' => $item['role'],
                    'content' => (string) $item['content'],
                ];
            }
        }

        // Append current prompt
        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        try {
            $response = $this->http
                ->withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout((int) config('services.groq.timeout', 20))
                ->post($baseUrl, [
                    'model' => $model,
                    'temperature' => 0.2,
                    'max_tokens' => 220,
                    'messages' => $messages,
                ]);

            if ($response->failed()) {
                // Log full response body to see EXACT error from Groq
                Log::error('Groq API Error Output', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            $response->throw();

            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || trim($content) === '') {
                throw new RuntimeException('The chatbot provider returned an empty response.');
            }

            return trim($content);
        } catch (\Throwable $exception) {
            $errorDetails = $exception->getMessage();

            if ($exception instanceof RequestException && $exception->response) {
                $errorDetails = $exception->response->body();
            }

            Log::warning('Chatbot provider request failed.', [
                'exception' => $exception::class,
                'response_body' => $errorDetails,
            ]);

            throw new RuntimeException('The chatbot is temporarily unavailable.', 0, $exception);
        }
    }
}
