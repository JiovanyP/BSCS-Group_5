<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index()
    {
        return view('admin.chat');
    }

    public function sendMessage(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        $n8nWebhookUrl = env('N8N_WEBHOOK_URL');

        try {
            $response = Http::post($n8nWebhookUrl, [
                'question' => $request->input('message'),
                
                // ADD THIS LINE: Create a unique session ID for this admin
                'sessionId' => 'admin_session_' . auth()->id(), 
            ]);

            return response()->json([
                'reply' => $response->json('output') ?? 'No response text found.'
            ]);

        } catch (\Exception $e) {
            return response()->json(['reply' => 'Error connecting to AI agent.'], 500);
        }
    }
}