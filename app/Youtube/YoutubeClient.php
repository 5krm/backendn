<?php

namespace App\Youtube;

use App\Models\YoutubeToken;
use Carbon\CarbonImmutable;
use Google\Client;
use Google_Service_YouTube;
use Illuminate\Http\Request;

class YoutubeClient
{
    public static function getAuthUrl(): string
    {
        $client = self::initClient();

        return $client->createAuthUrl();
    }

    public static function getAccessToken(string $code): void
    {
        $client = self::initClient();
        $result = $client->fetchAccessTokenWithAuthCode($code);

        $yearToExpire = 12 * 30 * 24 * 60 * 60;
        $tokenCreated = CarbonImmutable::createFromTimestamp($result['created']);

        YoutubeToken::query()->create([
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
            'token_type' => $result['token_type'],
            'created_at' => $tokenCreated,
            'expires_in' => $tokenCreated->addSeconds($result['expires_in']),
            'refresh_token_expires_in' => $tokenCreated->addSeconds($result['refresh_token_expires_in'] ?? $yearToExpire),
        ]);
    }

    /**
     * Get the youtube client
     *
     * @return array{Google_Service_YouTube, Client}
     */
    public static function getClient(): array
    {
        $token = YoutubeToken::query()->latest()->first();
        if (! $token) {
            throw new \Exception('No Youtube access has been granted.');
        }

        if ($token->needsRefresh()) {
            $token = self::refreshAccessToken($token->refresh_token);
        }

        $client = self::initClient();
        $client->setAccessToken($token->access_token);

        return [
            new Google_Service_YouTube($client),
            $client,
        ];
    }

    public static function refreshAccessToken(string $refreshToken): YoutubeToken
    {
        $client = self::initClient();
        $result = $client->fetchAccessTokenWithRefreshToken($refreshToken);

        $token = YoutubeToken::query()->latest()->first();
        if (! $token) {
            throw new \Exception('No Youtube access has been granted.');
        }

        $yearToExpire = 12 * 30 * 24 * 60 * 60;
        $tokenCreated = CarbonImmutable::createFromTimestamp($result['created']);

        $token->update([
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
            'token_type' => $result['token_type'],
            'created_at' => $tokenCreated,
            'expires_in' => $tokenCreated->addSeconds($result['expires_in']),
            'refresh_token_expires_in' => $tokenCreated->addSeconds($yearToExpire),
        ]);

        return $token;
    }

    public static function verify(Request $request): bool
    {
        $incomingState = $request->input('state');
        $expectedState = env('GOOGLE_AUTH_STATE');

        return $incomingState === $expectedState;
    }

    private static function initClient(): Client
    {
        $client = new Client;
        $client->setAuthConfig(storage_path('app/gc_client_secret.json'));
        $client->addScope(Google_Service_YouTube::YOUTUBE_UPLOAD);

        // The value must exactly match one of the authorized redirect URIs for the OAuth 2.0 client,
        // which you configured in your client's Cloud Console.
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));

        // offline access will give you both an access and refresh token so that
        // your app can refresh the access token without user interaction.
        $client->setAccessType('offline');

        // To make sure that the incoming connection is the result of an authentication request.
        $client->setState(env('GOOGLE_AUTH_STATE'));

        // To show the consent screen every time we request access.
        $client->setPrompt('consent');

        return $client;
    }
}
