<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    /**
     * Handle AI Chat request using Gemini API
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $apiKey = config('services.gemini.key');
        
        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'AI Configuration missing. Please set GEMINI_API_KEY in .env'
            ], 500);
        }

        $userMessage = $request->input('message');

        try {
            // System context to guide the AI behavior
            $systemPrompt = "Anda adalah Asisten AI untuk SIPS (Sarana Informasi Pengaduan Sekolah) SMKN 1 Bondowoso. "
                . "Tugas Anda adalah membantu pengguna memahami cara melaporkan masalah, memberikan informasi tentang sistem pengaduan, "
                . "dan menjawab pertanyaan seputar fasilitas sekolah dengan ramah, profesional, dan informatif. "
                . "Jika ditanya tentang teknis laporan, arahkan pengguna ke menu 'Buat Laporan'. "
                . "Gunakan bahasa Indonesia yang baik dan sopan.";

            $response = Http::asJson()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "SYSTEM INSTRUCTION: " . $systemPrompt . "\n\nUSER MESSAGE: " . $userMessage]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 500,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Gemini Success response:', $data); // Debug yang benar via Log
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Maaf, saya tidak bisa memproses permintaan Anda saat ini.";
                
                return response()->json([
                    'status' => 'success',
                    'reply' => $reply
                ]);
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? 'Terjadi kesalahan saat menghubungi server AI.';
            
            Log::error('Gemini API Error: ' . $response->body());

            // Handle specific status codes
            if ($response->status() === 429) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Waduh, jatah chat AI gratisan sedang penuh. Coba lagi dalam 30 detik ya!'
                ], 429);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'AI sedang istirahat sejenak. Silakan coba beberapa saat lagi.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('AI Controller Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan teknis pada sistem AI.'
            ], 500);
        }
    }
}
