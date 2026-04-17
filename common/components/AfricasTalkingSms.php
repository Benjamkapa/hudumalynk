<?php

namespace common\components;

use Yii;
use yii\base\Component;

/**
 * AfricasTalkingSms — SMS provider implementation via Africa's Talking API
 * To use a different provider, implement SmsInterface and update config.
 */
class AfricasTalkingSms extends Component implements SmsInterface
{
    public string $username  = 'sandbox';
    public string $apiKey    = '';
    public string $senderId  = 'HudumaLynk';
    public bool   $sandbox   = true;

    private string $apiUrl = 'https://api.africastalking.com/version1/messaging';
    private string $sandboxUrl = 'https://api.sandbox.africastalking.com/version1/messaging';

    public function init(): void
    {
        parent::init();
        $this->username = $_ENV['AFRICASTALKING_USERNAME'] ?? $this->username;
        $this->apiKey   = $_ENV['AFRICASTALKING_API_KEY']  ?? $this->apiKey;
        $this->senderId = $_ENV['AFRICASTALKING_SENDER_ID'] ?? $this->senderId;
        $this->sandbox  = ($_ENV['APP_ENV'] ?? 'development') !== 'production';
    }

    public function send(string $to, string $message): bool
    {
        $result = $this->sendBulk([$to], $message);
        return $result['sent'] > 0;
    }

    public function sendBulk(array $recipients, string $message): array
    {
        if (empty($this->apiKey)) {
            Yii::warning('[SMS] Africa\'s Talking API key not configured.', 'sms');
            return ['sent' => 0, 'failed' => count($recipients)];
        }

        $endpoint = $this->sandbox ? $this->sandboxUrl : $this->apiUrl;
        $payload  = [
            'username'  => $this->username,
            'to'        => implode(',', $recipients),
            'message'   => $message,
            'from'      => $this->senderId,
        ];

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_HTTPHEADER     => [
                'apiKey: ' . $this->apiKey,
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_TIMEOUT        => 15,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode !== 201 && $httpCode !== 200) {
            Yii::error('[SMS] AT API error: HTTP ' . $httpCode . ' — ' . $response, 'sms');
            return ['sent' => 0, 'failed' => count($recipients)];
        }

        $data  = json_decode($response, true);
        $sent  = 0;
        $failed = 0;
        $recipients_data = $data['SMSMessageData']['Recipients'] ?? [];
        foreach ($recipients_data as $r) {
            $r['status'] === 'Success' ? $sent++ : $failed++;
        }

        Yii::info('[SMS] Sent ' . $sent . ' / ' . count($recipients) . ' messages.', 'sms');
        return ['sent' => $sent, 'failed' => $failed];
    }
}
