<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Support\Str;
use App\Http\Requests\StoreUrlRequest;
use App\Http\Requests\UpdateUrlRequest;

class UrlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($code)
    {
        $url = Url::where('short_code', $code)->first();

        if ($url) {
            return redirect()->to($url->original_url, 301);
        }

        return response()->json([
            'error' => 'URL not found.'
        ], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUrlRequest $request)
    {
        $data = $request->validated();

        $urlExists = Url::where('original_url', $data['url'])->first();
        if ($urlExists) {
            return response()->json([
                'short_url' => url('/') . '/' . $urlExists->short_code
            ], 200);
        }

        do {
            $shortCode = Str::random(6);
        } while (Url::where('short_code', $shortCode)->exists());

        $url = Url::create([
            'original_url' => $data['url'],
            'short_code' => $shortCode
        ]);

        return response()->json([
            'short_url' => url('/') . '/' . $url->short_code
        ], 201);
    }
}
