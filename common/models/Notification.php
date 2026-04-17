<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Notification — in-app notification log (also tracks SMS/email send state)
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $type
 * @property string $title
 * @property string $message
 * @property array  $data
 * @property bool   $is_read
 * @property bool   $sent_via_sms
 * @property bool   $sent_via_email
 * @property string $created_at
 */
class Notification extends ActiveRecord
{
    // Notification types (constants for easy reference)
    const TYPE_ORDER_PLACED      = 'order_placed';
    const TYPE_ORDER_ACCEPTED    = 'order_accepted';
    const TYPE_ORDER_REJECTED    = 'order_rejected';
    const TYPE_ORDER_COMPLETED   = 'order_completed';
    const TYPE_ORDER_CANCELLED   = 'order_cancelled';
    const TYPE_PAYMENT_CONFIRMED = 'payment_confirmed';
    const TYPE_PAYMENT_FAILED    = 'payment_failed';
    const TYPE_REVIEW_RECEIVED   = 'review_received';
    const TYPE_SUB_EXPIRING      = 'subscription_expiring';
    const TYPE_SUB_EXPIRED       = 'subscription_expired';
    const TYPE_PROVIDER_APPROVED = 'provider_approved';
    const TYPE_PROVIDER_REJECTED = 'provider_rejected';
    const TYPE_GENERAL           = 'general';

    public static function tableName(): string
    {
        return '{{%notifications}}';
    }

    public function rules(): array
    {
        return [
            [['user_id', 'type', 'title', 'message'], 'required'],
            ['user_id',        'integer'],
            ['type',           'string', 'max' => 50],
            ['title',          'string', 'max' => 255],
            ['message',        'string'],
            ['data',           'safe'],
            ['is_read',        'boolean'],
            ['sent_via_sms',   'boolean'],
            ['sent_via_email', 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title'          => 'Title',
            'message'        => 'Message',
            'is_read'        => 'Read',
            'sent_via_sms'   => 'SMS Sent',
            'sent_via_email' => 'Email Sent',
            'created_at'     => 'Sent At',
        ];
    }

    public function markRead(): void
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->save(false, ['is_read']);
        }
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
