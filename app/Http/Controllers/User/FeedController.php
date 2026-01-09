<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\PropertyFeedGenerator;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class FeedController extends Controller
{
    public function googleMerchantBrasil(): Response
    {
        $generator = PropertyFeedGenerator::forBrasil();
        $xml = $generator->googleMerchant();
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function googleMerchantParaguai(): Response
    {
        $generator = PropertyFeedGenerator::forParaguai();
        $xml = $generator->googleMerchant();
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function googleMerchant(): Response
    {
        $generator = PropertyFeedGenerator::all();
        $xml = $generator->googleMerchant();
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function jsonBrasil(): JsonResponse
    {
        $generator = PropertyFeedGenerator::forBrasil();
        $data = $generator->json();
        
        return response()->json($data, 200, [
            'Cache-Control' => 'public, max-age=3600',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    public function jsonParaguai(): JsonResponse
    {
        $generator = PropertyFeedGenerator::forParaguai();
        $data = $generator->json();
        
        return response()->json($data, 200, [
            'Cache-Control' => 'public, max-age=3600',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    public function json(): JsonResponse
    {
        $generator = PropertyFeedGenerator::all();
        $data = $generator->json();
        
        return response()->json($data, 200, [
            'Cache-Control' => 'public, max-age=3600',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    public function health(): JsonResponse
    {
        $allGenerator = PropertyFeedGenerator::all();
        $allData = $allGenerator->json();

        return response()->json([
            'status' => 'ok',
            'service' => 'Terras no Paraguay - Property Feeds',
            'timestamp' => now()->toIso8601String(),
            'total_properties' => $allData['meta']['total'],
        ]);
    }
 public function dashboard()
    {
        $allGenerator = \App\Services\PropertyFeedGenerator::all();
        $brasilGenerator = \App\Services\PropertyFeedGenerator::forBrasil();
        $paraguaiGenerator = \App\Services\PropertyFeedGenerator::forParaguai();

        $stats = [
            'all' => $allGenerator->json(),
            'brasil' => $brasilGenerator->json(),
            'paraguai' => $paraguaiGenerator->json(),
        ];

        return view('feeds.dashboard', compact('stats'));
    }
}
