<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ChatbotController extends Controller
{
    public function __construct(private readonly ChatbotService $chatbot)
    {
    }

    public function guest(Request $request): JsonResponse
    {
        return $this->respond($request);
    }

    public function authenticated(Request $request): JsonResponse
    {
        return $this->respond($request);
    }

    private function respond(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['sometimes', 'array', 'max:8'],
            'history.*.role' => ['required', 'string', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:2000'],
        ]);

        try {
            return response()->json([
                'message' => $this->chatbot->reply(
                    $validated['message'],
                    $validated['history'] ?? [],
                ),
            ]);
        } catch (Throwable) {
            return response()->json([
                'message' => 'The support assistant is temporarily unavailable. Please try again shortly.',
            ], 503);
        }
    }
}
