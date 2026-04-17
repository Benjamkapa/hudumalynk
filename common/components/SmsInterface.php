<?php

namespace common\components;

/**
 * SmsInterface — contract for any SMS provider
 * To switch providers, implement this interface and swap the class in config.
 */
interface SmsInterface
{
    /**
     * Send a single SMS message.
     *
     * @param string $to      Recipient phone number (international format, e.g. +254712345678)
     * @param string $message Message text (max ~160 chars for single SMS)
     * @return bool           True on success
     */
    public function send(string $to, string $message): bool;

    /**
     * Send a bulk SMS to multiple recipients.
     *
     * @param array  $recipients Array of phone numbers
     * @param string $message
     * @return array             ['sent' => n, 'failed' => n]
     */
    public function sendBulk(array $recipients, string $message): array;
}
