<?php

namespace App\Http\Controllers\App;

use App\Youtube\YoutubeClient;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class YoutubeController
{
    public function index()
    {
        // if (!auth()->check() || !auth()->user()->isAdmin()) {
        //     throw new NotFoundHttpException();
        // }

        $authUrl = '';
        $mode = session('mode', 'request');
        if ($mode == 'request') {
            $authUrl = YoutubeClient::getAuthUrl();
        }

        return view('app.youtube.index', [
            'mode' => $mode,
            'authUrl' => $authUrl,
        ]);
    }

    public function callback(Request $request)
    {
        $valid = YoutubeClient::verify($request);
        if (! $valid) {
            return response('Invalid request', 401);
        }

        YoutubeClient::getAccessToken($request->code);

        return redirect()
            ->route('youtube.index')
            ->with('mode', 'done');
    }
}
