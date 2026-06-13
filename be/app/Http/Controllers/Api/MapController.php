<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    protected $apiKey = '6TTIZbUWJmRMSpiYzQ44YY8z5v8wv43w0';

    public function geocodeForward(Request $request)
    {
        $text = $request->query('text');
        if (!$text) {
            return response()->json(['message' => 'Query text is required'], 400);
        }

        $response = Http::get('https://mapapis.openmap.vn/v1/geocode/forward', [
            'text' => $text,
            'apikey' => $this->apiKey
        ]);

        return response($response->body(), $response->status())
            ->header('Content-Type', 'application/json');
    }

    public function geocodeReverse(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if (!$lat || !$lng) {
            return response()->json(['message' => 'Latitude and Longitude are required'], 400);
        }

        $response = Http::get('https://mapapis.openmap.vn/v1/geocode/reverse', [
            'latlng' => "{$lat},{$lng}",
            'apikey' => $this->apiKey
        ]);

        return response($response->body(), $response->status())
            ->header('Content-Type', 'application/json');
    }
}
