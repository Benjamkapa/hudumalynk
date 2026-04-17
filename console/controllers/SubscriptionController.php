<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use common\models\Subscription;
use common\models\Provider;
use common\models\Listing;
use common\models\Notification;

/**
 * Subscription cron — expire subscriptions and notify expiring ones
 * Run via: php yii subscription/check-expiry
 */
class SubscriptionController extends Controller
{
    /**
     * Expire overdue subscriptions and deactivate provider listings.
     * Schedule: daily at midnight  (cron: 0 0 * * *)
     */
    public function actionCheckExpiry(): int
    {
        $expired = Subscription::find()
            ->where(['status' => Subscription::STATUS_ACTIVE])
            ->andWhere(['<', 'end_date', date('Y-m-d H:i:s')])
            ->all();

        $count = 0;
        foreach ($expired as $sub) {
            $sub->status = Subscription::STATUS_EXPIRED;
            $sub->save(false);

            // Deactivate all provider listings
            Listing::updateAll(
                ['status' => Listing::STATUS_INACTIVE],
                ['provider_id' => $sub->provider_id]
            );

            // Notify provider
            if ($sub->provider?->user) {
                Yii::$app->sms->notify(
                    $sub->provider->user,
                    Notification::TYPE_SUB_EXPIRED,
                    [],
                    true,
                    true
                );
            }
            $count++;
        }

        echo "[Subscription] Expired {$count} subscription(s).\n";
        return ExitCode::OK;
    }

    /**
     * Send 7-day expiry reminders.
     * Schedule: daily at 9am  (cron: 0 9 * * *)
     */
    public function actionSendReminders(): int
    {
        $window = date('Y-m-d H:i:s', strtotime('+7 days'));
        $subs   = Subscription::find()
            ->with(['provider.user'])
            ->where(['status' => Subscription::STATUS_ACTIVE])
            ->andWhere(['<=', 'end_date', $window])
            ->andWhere(['>=', 'end_date', date('Y-m-d H:i:s')])
            ->all();

        $count = 0;
        foreach ($subs as $sub) {
            if ($sub->provider?->user) {
                Yii::$app->sms->subscriptionExpiring($sub->provider->user, $sub->daysRemaining());
                $count++;
            }
        }

        echo "[Subscription] Sent {$count} expiry reminder(s).\n";
        return ExitCode::OK;
    }
}
