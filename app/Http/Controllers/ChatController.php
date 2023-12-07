<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class ChatController extends Controller
{
    /**
     * @param Request $request
     */
    public function __invoke(Request $request)
    {
        try {
            /** @var array $response */
            $response = Http::withOptions([
                'base_uri' => 'https://openai.wndbac.cn', // Set the proxy URL
            ])->withHeaders([
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . env('CHAT_GPT_KEY')
            ])->post('https://api.openai.com/v1/chat/completions', [
                "model" => $request->post('model'),
                "messages" => $request->post('messages'),
                "temperature" => 0,
                "max_tokens" => 2048
            ])->json(); // Use json() to parse the response as JSON

            return $response['choices'][0]['message'];
        } catch (Throwable $e) {
            return response()->json([
            'role' => 'assistant',
            'content' => "Uhm sorry ik begrijp niet helemaal wat je bedoelt"
        ]);
        }
    }
}
