<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\Request;
use GuzzleHttp\Client;


class Transcriber extends Model
{
    use HasFactory;

    public function convertMp3ToText($filePath)
    {
        // Ensure that the file exists before processing
        if (!file_exists($filePath)) {
            return response()->json([
                'error' => 'File not found.'
            ], 404);
        }

        // Get the file name from the provided file path
        $fileName = basename($filePath);

        // OpenAI API key from environment file
        $apiKey = env('OPENAI_API_KEY');

        // Set up the Guzzle client
        $client = new Client();

        try {
            // Prepare the multipart form-data for the request
            $response = $client->post('https://api.openai.com/v1/audio/transcriptions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => $fileName
                    ],
                    [
                        'name' => 'model',
                        'contents' => 'whisper-1' // Using OpenAI Whisper model
                    ],
                ],
            ]);

            // Get the response from OpenAI API
            $responseBody = json_decode($response->getBody(), true);

            // Return the transcribed text
            return response()->json([
                'text' => $responseBody['text']
            ]);

        } catch (\Exception $e) {
            // Handle any errors that occur during the API request
            return response()->json([
                'error' => 'Error processing audio file',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
