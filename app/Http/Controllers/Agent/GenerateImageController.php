<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AiService;
use Illuminate\Support\Facades\File;

class GenerateImageController extends Controller
{

    public function generateImage(Request $request, AiService $aiServices)
    {

        $validatedData = $request->validate([
            'image_prompt' => 'required|string|max:1000',
            'art_style' => 'required|string',
            'lighting' => 'required|string',
            'camera_angle' => 'required|string',
            'image_size' => 'required|string',
            'num_images' => 'required|integer|min:1|max:4',
            'image_type' => 'required|string|in:gallery,thumbnail,floor_plan,video_poster,floor_planning_gallery',
        ]);

        try {
            // Build enhanced prompt
            $enhancedPrompt = $this->buildPrompt($validatedData);

            // Generate images using Pollinations
            $generatedImages = $aiServices->generateImages(
                $enhancedPrompt,
                $validatedData['num_images'],
                $validatedData['image_size']
            );

            // Save images and get URLs
            $imageUrls = [];
            foreach ($generatedImages as $index => $imageResult) {
                if ($imageResult['status'] === 'error') {
                    throw new \Exception($imageResult['message']);
                }

                $savedImage = $this->saveImage(
                    $imageResult['base64Data'],
                    $validatedData['image_type'],
                    $index
                );

                $imageUrls[] = [
                    'url' => $savedImage['url'],
                    'path' => $savedImage['relativePath']
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => count($imageUrls) > 1
                    ? count($imageUrls) . ' ' . __('images generated successfully') . '!'
                    : __('Image generated successfully') . '!',
                'images' => $imageUrls,
                'image_type' => $validatedData['image_type']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate images: ' . $e->getMessage()
            ], 500);
        }
    }

    private function buildPrompt(array $data)
    {
        $basePrompt = $data['image_prompt'];
        $style = str_replace('-', ' ', $data['art_style']);
        $lighting = str_replace('-', ' ', $data['lighting']);
        $angle = str_replace('-', ' ', $data['camera_angle']);

        return "{$basePrompt}, {$style} style, {$lighting} lighting, {$angle} camera angle, professional real estate photography, high quality, detailed";
    }

    /**
     * Save generated image to storage
     */
    private function saveImage($base64Data, $imageType, $index)
    {
        // Create directory based on image type
        $directory = public_path('assets/img/properties/ai-generated/' . $imageType);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Generate unique filename
        $fileName = 'ai_' . $imageType . '_' . time() . '_' . $index . '_' . uniqid() . '.png';
        $filePath = $directory . '/' . $fileName;

        // Decode and save the image
        $imageData = base64_decode($base64Data);
        file_put_contents($filePath, $imageData);

        // Optimize image size 
        $this->optimizeImage($filePath);

        // Return both absolute URL and relative path
        return [
            'url' => asset('assets/img/properties/ai-generated/' . $imageType . '/' . $fileName),
            'relativePath' => 'assets/img/properties/ai-generated/' . $imageType . '/' . $fileName,
            'fileName' => $fileName
        ];
    }

    /**
     * Optimize image to reduce file size 
     */
    private function optimizeImage($filePath)
    {
        try {
            // Check if GD library is available
            if (!extension_loaded('gd')) {
                return;
            }

            $imageInfo = getimagesize($filePath);

            if (!$imageInfo) {
                return;
            }

            $mimeType = $imageInfo['mime'];

            // Create image resource based on type
            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($filePath);
                    imagejpeg($image, $filePath, 85);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($filePath);
                    imagepng($image, $filePath, 6);
                    break;
            }

            if (isset($image)) {
                imagedestroy($image);
            }
        } catch (\Exception $e) {
            \Log::warning('Image optimization failed: ' . $e->getMessage());
        }
    }
}
