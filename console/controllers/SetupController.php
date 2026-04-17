<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use common\models\User;
use common\models\SubscriptionPlan;
use common\models\Setting;

/**
 * Setup — seeds initial data (admin user, subscription plans, settings)
 * Run once: php yii setup/init
 */
class SetupController extends Controller
{
    public function actionInit(): int
    {
        echo "🚀 HudumaLynk Setup\n";
        echo str_repeat('─', 50) . "\n\n";

        $this->seedAdmin();
        $this->seedPlans();
        $this->seedSettings();

        $adminEmail = $_ENV['ADMIN_EMAIL']    ?? 'admin@hudumalynk.co.ke';
        $adminPass  = $_ENV['ADMIN_PASS']     ?? ($_ENV['ADMIN_PASSWORD'] ?? 'Admin@123');
        echo "\n✅ Setup complete! Admin: " . $adminEmail . " / " . $adminPass . "\n";
        return ExitCode::OK;
    }

    private function seedAdmin(): void
    {
        echo "Creating superadmin... ";
        if (User::find()->where(['role' => User::ROLE_ADMIN])->exists()) {
            echo "exists (skipping) ✓\n";
            return;
        }
        $user             = new User();
        $user->first_name = $_ENV['ADMIN_FIRST_NAME'] ?? 'HudumaLynk';
        $user->last_name  = $_ENV['ADMIN_LAST_NAME']  ?? 'Admin';
        $user->email      = $_ENV['ADMIN_EMAIL']      ?? 'admin@hudumalynk.co.ke';
        $user->phone      = $_ENV['ADMIN_PHONE']      ?? '+254700000000';
        $user->role       = User::ROLE_ADMIN;
        $user->status     = User::STATUS_ACTIVE;
        $user->setPassword($_ENV['ADMIN_PASS'] ?? ($_ENV['ADMIN_PASSWORD'] ?? 'Admin@123'));
        $user->generateAuthKey();
        $user->save(false);
        echo "created ✓\n";
    }

    private function seedPlans(): void
    {
        echo "→ Subscription plans... ";
        if (SubscriptionPlan::find()->count() > 0) {
            echo "already seeded.\n";
            return;
        }
        $plans = [
            ['name' => 'Basic',        'slug' => 'basic',        'price_kes' => 1000, 'duration_days' => 30,  'max_products' => 5,   'max_services' => 3,  'featured_slots' => 0, 'is_popular' => false, 'description' => 'Great for individuals starting out.'],
            ['name' => 'Professional', 'slug' => 'professional', 'price_kes' => 2500, 'duration_days' => 30,  'max_products' => 20,  'max_services' => 10, 'featured_slots' => 1, 'is_popular' => true,  'description' => 'Best for growing businesses.'],
            ['name' => 'Premium',      'slug' => 'premium',      'price_kes' => 5000, 'duration_days' => 30,  'max_products' => 999, 'max_services' => 999,'featured_slots' => 3, 'is_popular' => false, 'description' => 'Unlimited listings + maximum visibility.'],
        ];
        foreach ($plans as $p) {
            $plan = new SubscriptionPlan($p);
            $plan->status = SubscriptionPlan::STATUS_ACTIVE;
            $plan->save(false);
        }
        echo "seeded ✓\n";
    }

    private function seedSettings(): void
    {
        echo "→ Default settings... ";
        $defaults = [
            'commission_rate'     => '10',
            'cod_max_amount'      => '2000',
            'deposit_max_amount'  => '10000',
            'deposit_percent'     => '30',
            'kes_to_usd_rate'     => '0.0077',
            'platform_name'       => 'HudumaLynk',
            'support_email'       => 'support@hudumalynk.co.ke',
            'support_phone'       => '+254700000000',
        ];
        foreach ($defaults as $k => $v) {
            if (!Setting::findOne(['key' => $k])) {
                Setting::set($k, $v);
            }
        }
        echo "seeded ✓\n";
    }
}
