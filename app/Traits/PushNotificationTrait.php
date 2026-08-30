<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait PushNotificationTrait
{
    /**
     * Dispatch FCM Push Notification
     *
     * @param string $deviceToken
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function dispatchPushNotification($deviceToken, $title, $body, $data = [])
    {
        // Example basic implementation for FCM
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $fcmKey = config('services.fcm.key'); // Assume FCM key is in config

        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        $fcmData = [
            'to' => $deviceToken,
            'notification' => $notification,
            'data' => $data,
        ];

        $headers = [
            'Authorization: key=' . $fcmKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmData));

        $result = curl_exec($ch);
        
        if ($result === false) {
            Log::error('FCM Send Error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        Log::info('FCM Send Success: ' . $result);

        return true;
    }
}
