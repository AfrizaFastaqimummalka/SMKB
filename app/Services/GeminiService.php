<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('gemini.api_key');
        $this->model = (string) config('gemini.model');
        $this->baseUrl = (string) config('gemini.base_url');
        $this->timeout = (int) config('gemini.timeout_seconds');
    }

    public function isConfigured(): bool
    {
        return $this->apiKey !== '' && $this->apiKey !== 'dummy-gemini-api-key';
    }

    public function generateJson(string $systemInstruction, string $userPrompt, array $schema): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemInstruction]],
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $userPrompt]]],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'responseSchema' => $schema,
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('Gemini generateJson gagal', ['status' => $response->status(), 'body' => $response->body()]);

                return null;
            }

            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

            return $text ? json_decode($text, true) : null;
        } catch (\Throwable $e) {
            Log::warning('Gemini generateJson exception', ['message' => $e->getMessage()]);

            return null;
        }
    }

    public function generateText(string $systemInstruction, string $userPrompt): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemInstruction]],
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $userPrompt]]],
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('Gemini generateText gagal', ['status' => $response->status(), 'body' => $response->body()]);

                return null;
            }

            return data_get($response->json(), 'candidates.0.content.parts.0.text');
        } catch (\Throwable $e) {
            Log::warning('Gemini generateText exception', ['message' => $e->getMessage()]);

            return null;
        }
    }
}
