<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class GetAudioFile extends Model
{
    use HasFactory;

    public function getFileFromS3($url)
    {
        if(!$url){
            return false;
        }
        // Initialize the S3 client
        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'), 
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        // Parse the URL to get the bucket name and key
        $parsedUrl = parse_url($url);
        $bucket = explode('.', $parsedUrl['host'])[0];
        $key = ltrim($parsedUrl['path'], '/');

        \Log::info('Bucket: ' . $bucket);
        \Log::info('Key: ' . $key);

        try {
            // Generate a pre-signed URL for the file
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key'    => $key,
            ]);

            $request = $s3Client->createPresignedRequest($cmd, '+30 minutes');

            // Make the URL publicly accessible
            $signedUrl = (string) $request->getUri();
            $publicUrl = str_replace($s3Client->getEndpoint(), "https://{$bucket}.s3.amazonaws.com", $signedUrl);

            // Return the public URL to transcriber in commands folder
            return $publicUrl;

        } catch (AwsException $e) {
            // Handle the error
            return 'Error: ' . $e->getMessage();
        }
    }

    public function downloadFile($url)
    {
        // Get the pre-signed URL for the file
        $signedUrl = $this->getFileFromS3($url);

        // Redirect the user to the pre-signed URL
        return redirect($signedUrl);
    }
}
