<?php
/**
 * HudumaLynk — Complete Database Schema Migration
 * Creates all tables in FK-safe order with seed data for plans and settings
 */

use yii\db\Migration;

class m260409_000000_create_all_tables extends Migration
{
    public function safeUp()
    {
        $this->createUsersTable();
        $this->createCategoriesTable();
        $this->createProvidersTable();
        $this->createSubscriptionPlansTable();
        $this->createSubscriptionsTable();
        $this->createListingsTable();
        $this->createListingImagesTable();
        $this->createOrdersTable();
        $this->createOrderItemsTable();
        $this->createPaymentsTable();
        $this->createCommissionsTable();
        $this->createDeliveriesTable();
        $this->createReviewsTable();
        $this->createNotificationsTable();
        $this->createFeaturedListingsTable();
        $this->createSettingsTable();
        $this->seedData();
    }

    public function safeDown()
    {
        $this->dropTable('{{%featured_listings}}');
        $this->dropTable('{{%notifications}}');
        $this->dropTable('{{%reviews}}');
        $this->dropTable('{{%deliveries}}');
        $this->dropTable('{{%commissions}}');
        $this->dropTable('{{%payments}}');
        $this->dropTable('{{%order_items}}');
        $this->dropTable('{{%orders}}');
        $this->dropTable('{{%listing_images}}');
        $this->dropTable('{{%listings}}');
        $this->dropTable('{{%subscriptions}}');
        $this->dropTable('{{%subscription_plans}}');
        $this->dropTable('{{%providers}}');
        $this->dropTable('{{%categories}}');
        $this->dropTable('{{%settings}}');
        $this->dropTable('{{%users}}');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createUsersTable(): void
    {
        $this->createTable('{{%users}}', [
            'id'                     => $this->bigPrimaryKey(),
            'first_name'             => $this->string(100)->notNull(),
            'last_name'              => $this->string(100)->notNull(),
            'email'                  => $this->string(150)->unique(),
            'phone'                  => $this->string(20)->unique(),
            'password_hash'          => $this->string(255)->notNull(),
            'auth_key'               => $this->string(32)->notNull()->defaultValue(''),
            'password_reset_token'   => $this->string(255)->null(),
            'verification_token'     => $this->string(255)->null(),
            'role'                   => "ENUM('customer','provider','admin') NOT NULL DEFAULT 'customer'",
            'status'                 => "ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active'",
            'created_at'             => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'             => $this->dateTime()->null(),
        ]);
        $this->createIndex('idx_users_role',   '{{%users}}', 'role');
        $this->createIndex('idx_users_status', '{{%users}}', 'status');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createCategoriesTable(): void
    {
        $this->createTable('{{%categories}}', [
            'id'          => $this->primaryKey(),
            'name'        => $this->string(100)->notNull(),
            'slug'        => $this->string(120)->notNull()->unique(),
            'description' => $this->text()->null(),
            'icon'        => $this->string(100)->null(),   // e.g. Bootstrap icon class
            'image'       => $this->string(255)->null(),
            'parent_id'   => $this->integer()->null(),
            'sort_order'  => $this->smallInteger()->notNull()->defaultValue(0),
            'type'        => "ENUM('service','product','both') NOT NULL DEFAULT 'both'",
            'status'      => "ENUM('active','inactive') NOT NULL DEFAULT 'active'",
            'created_at'  => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_categories_parent', '{{%categories}}', 'parent_id', '{{%categories}}', 'id', 'SET NULL', 'CASCADE');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createProvidersTable(): void
    {
        $this->createTable('{{%providers}}', [
            'id'                => $this->bigPrimaryKey(),
            'user_id'           => $this->bigInteger()->notNull(),
            'business_name'     => $this->string(255)->notNull(),
            'slug'              => $this->string(300)->notNull()->unique(),
            'description'       => $this->text()->null(),
            'city'              => $this->string(100)->notNull()->defaultValue('Nairobi'),
            'address'           => $this->text()->null(),
            'phone'             => $this->string(20)->null(),
            'email'             => $this->string(150)->null(),
            'website'           => $this->string(255)->null(),
            'logo'              => $this->string(255)->null(),
            'id_document'       => $this->string(255)->null(),   // uploaded file path
            'is_verified'       => $this->boolean()->notNull()->defaultValue(false),
            'rating'            => $this->decimal(3, 2)->notNull()->defaultValue(0),
            'total_reviews'     => $this->integer()->notNull()->defaultValue(0),
            'status'            => "ENUM('pending','active','suspended','rejected') NOT NULL DEFAULT 'pending'",
            'rejection_reason'  => $this->text()->null(),
            'created_at'        => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'        => $this->dateTime()->null(),
        ]);
        $this->addForeignKey('fk_providers_user', '{{%providers}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('idx_providers_status', '{{%providers}}', 'status');
        $this->createIndex('idx_providers_city',   '{{%providers}}', 'city');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createSubscriptionPlansTable(): void
    {
        $this->createTable('{{%subscription_plans}}', [
            'id'             => $this->primaryKey(),
            'name'           => $this->string(100)->notNull(),
            'slug'           => $this->string(100)->notNull()->unique(),
            'description'    => $this->text()->null(),
            'price_kes'      => $this->decimal(10, 2)->notNull(),
            'duration_days'  => $this->integer()->notNull()->defaultValue(30),
            'max_products'   => $this->integer()->notNull()->defaultValue(5),
            'max_services'   => $this->integer()->notNull()->defaultValue(3),
            'featured_slots' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_popular'     => $this->boolean()->notNull()->defaultValue(false),
            'status'         => "ENUM('active','inactive') NOT NULL DEFAULT 'active'",
            'created_at'     => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createSubscriptionsTable(): void
    {
        $this->createTable('{{%subscriptions}}', [
            'id'             => $this->bigPrimaryKey(),
            'provider_id'    => $this->bigInteger()->notNull(),
            'plan_id'        => $this->integer()->notNull(),
            'start_date'     => $this->dateTime()->notNull(),
            'end_date'       => $this->dateTime()->notNull(),
            'status'         => "ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active'",
            'payment_method' => "ENUM('mpesa','card','cash','manual') DEFAULT 'mpesa'",
            'transaction_id' => $this->string(255)->null(),
            'amount_paid'    => $this->decimal(10, 2)->null(),
            'created_at'     => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_subscriptions_provider', '{{%subscriptions}}', 'provider_id', '{{%providers}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_subscriptions_plan',     '{{%subscriptions}}', 'plan_id',     '{{%subscription_plans}}', 'id', 'RESTRICT', 'CASCADE');
        $this->createIndex('idx_subscriptions_status',   '{{%subscriptions}}', 'status');
        $this->createIndex('idx_subscriptions_end_date', '{{%subscriptions}}', 'end_date');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createListingsTable(): void
    {
        $this->createTable('{{%listings}}', [
            'id'             => $this->bigPrimaryKey(),
            'provider_id'    => $this->bigInteger()->notNull(),
            'category_id'    => $this->integer()->notNull(),
            'type'           => "ENUM('product','service') NOT NULL",
            'name'           => $this->string(255)->notNull(),
            'slug'           => $this->string(300)->notNull(),
            'description'    => $this->text()->null(),
            'price'          => $this->decimal(10, 2)->notNull(),
            'stock_quantity' => $this->integer()->null(),          // products only
            'availability'   => $this->string(255)->null(),        // services only
            'location'       => $this->string(255)->null(),
            'is_featured'    => $this->boolean()->notNull()->defaultValue(false),
            'views'          => $this->integer()->notNull()->defaultValue(0),
            'status'         => "ENUM('active','inactive','draft') NOT NULL DEFAULT 'draft'",
            'created_at'     => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'     => $this->dateTime()->null(),
        ]);
        $this->addForeignKey('fk_listings_provider', '{{%listings}}', 'provider_id', '{{%providers}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_listings_category', '{{%listings}}', 'category_id', '{{%categories}}', 'id', 'RESTRICT', 'CASCADE');
        $this->createIndex('idx_listings_type',        '{{%listings}}', 'type');
        $this->createIndex('idx_listings_status',      '{{%listings}}', 'status');
        $this->createIndex('idx_listings_is_featured', '{{%listings}}', 'is_featured');
        $this->createIndex('idx_listings_slug',        '{{%listings}}', ['provider_id', 'slug'], true);
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createListingImagesTable(): void
    {
        $this->createTable('{{%listing_images}}', [
            'id'         => $this->bigPrimaryKey(),
            'listing_id' => $this->bigInteger()->notNull(),
            'image_path' => $this->string(500)->notNull(),
            'is_primary' => $this->boolean()->notNull()->defaultValue(false),
            'sort_order' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_listing_images_listing', '{{%listing_images}}', 'listing_id', '{{%listings}}', 'id', 'CASCADE', 'CASCADE');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createOrdersTable(): void
    {
        $this->createTable('{{%orders}}', [
            'id'               => $this->bigPrimaryKey(),
            'user_id'          => $this->bigInteger()->notNull(),
            'provider_id'      => $this->bigInteger()->notNull(),
            'reference'        => $this->string(30)->notNull()->unique(), // e.g. HL-2024-00001
            'total_amount'     => $this->decimal(10, 2)->notNull(),
            'deposit_amount'   => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'balance_amount'   => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'payment_type'     => "ENUM('full','partial','delivery') NOT NULL",
            'status'           => "ENUM('pending','awaiting_payment','awaiting_deposit','deposit_paid','processing','out_for_delivery','completed','cancelled','failed') NOT NULL DEFAULT 'pending'",
            'payment_status'   => "ENUM('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid'",
            'delivery_address' => $this->text()->null(),
            'notes'            => $this->text()->null(),
            'currency'         => $this->string(3)->notNull()->defaultValue('KES'),
            'created_at'       => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'       => $this->dateTime()->null(),
        ]);
        $this->addForeignKey('fk_orders_user',     '{{%orders}}', 'user_id',     '{{%users}}',     'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_orders_provider', '{{%orders}}', 'provider_id', '{{%providers}}', 'id', 'RESTRICT', 'CASCADE');
        $this->createIndex('idx_orders_status',         '{{%orders}}', 'status');
        $this->createIndex('idx_orders_payment_status', '{{%orders}}', 'payment_status');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createOrderItemsTable(): void
    {
        $this->createTable('{{%order_items}}', [
            'id'         => $this->bigPrimaryKey(),
            'order_id'   => $this->bigInteger()->notNull(),
            'listing_id' => $this->bigInteger()->null(),   // nullable (listing may be deleted)
            'name'       => $this->string(255)->notNull(), // snapshot of name at order time
            'type'       => "ENUM('product','service') NOT NULL",
            'quantity'   => $this->integer()->notNull()->defaultValue(1),
            'price'      => $this->decimal(10, 2)->notNull(),
            'total'      => $this->decimal(10, 2)->notNull(),
        ]);
        $this->addForeignKey('fk_order_items_order',   '{{%order_items}}', 'order_id',   '{{%orders}}',   'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_order_items_listing', '{{%order_items}}', 'listing_id', '{{%listings}}', 'id', 'SET NULL', 'CASCADE');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createPaymentsTable(): void
    {
        $this->createTable('{{%payments}}', [
            'id'             => $this->bigPrimaryKey(),
            'order_id'       => $this->bigInteger()->notNull(),
            'amount'         => $this->decimal(10, 2)->notNull(),
            'currency'       => $this->string(3)->notNull()->defaultValue('KES'),
            'payment_stage'  => "ENUM('deposit','balance','full','delivery') NOT NULL",
            'method'         => "ENUM('mpesa','card','cash') NOT NULL",
            'transaction_id' => $this->string(255)->null(),
            'phone_number'   => $this->string(20)->null(),    // for mpesa
            'mpesa_receipt'  => $this->string(100)->null(),   // mpesa receipt number
            'status'         => "ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending'",
            'failure_reason' => $this->string(500)->null(),
            'paid_at'        => $this->dateTime()->null(),
            'created_at'     => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_payments_order', '{{%payments}}', 'order_id', '{{%orders}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('idx_payments_transaction_id', '{{%payments}}', 'transaction_id');
        $this->createIndex('idx_payments_status',         '{{%payments}}', 'status');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createCommissionsTable(): void
    {
        $this->createTable('{{%commissions}}', [
            'id'           => $this->bigPrimaryKey(),
            'order_id'     => $this->bigInteger()->notNull(),
            'provider_id'  => $this->bigInteger()->notNull(),
            'order_amount' => $this->decimal(10, 2)->notNull(),
            'rate'         => $this->decimal(5, 2)->notNull(),    // e.g. 10.00 = 10%
            'amount'       => $this->decimal(10, 2)->notNull(),
            'status'       => "ENUM('pending','paid') NOT NULL DEFAULT 'pending'",
            'paid_at'      => $this->dateTime()->null(),
            'created_at'   => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_commissions_order',    '{{%commissions}}', 'order_id',    '{{%orders}}',    'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_commissions_provider', '{{%commissions}}', 'provider_id', '{{%providers}}', 'id', 'CASCADE', 'CASCADE');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createDeliveriesTable(): void
    {
        $this->createTable('{{%deliveries}}', [
            'id'             => $this->bigPrimaryKey(),
            'order_id'       => $this->bigInteger()->notNull(),
            'driver_name'    => $this->string(150)->null(),
            'driver_phone'   => $this->string(20)->null(),
            'status'         => "ENUM('pending','picked','out_for_delivery','delivered','failed') NOT NULL DEFAULT 'pending'",
            'notes'          => $this->text()->null(),
            'estimated_time' => $this->string(100)->null(),
            'created_at'     => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'     => $this->dateTime()->null(),
        ]);
        $this->addForeignKey('fk_deliveries_order', '{{%deliveries}}', 'order_id', '{{%orders}}', 'id', 'CASCADE', 'CASCADE');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createReviewsTable(): void
    {
        $this->createTable('{{%reviews}}', [
            'id'          => $this->bigPrimaryKey(),
            'user_id'     => $this->bigInteger()->notNull(),
            'provider_id' => $this->bigInteger()->notNull(),
            'order_id'    => $this->bigInteger()->notNull()->unique(), // one review per order
            'rating'      => $this->smallInteger()->notNull(),         // 1–5
            'title'       => $this->string(255)->null(),
            'comment'     => $this->text()->null(),
            'is_verified' => $this->boolean()->notNull()->defaultValue(true), // order-verified
            'status'      => "ENUM('visible','hidden') NOT NULL DEFAULT 'visible'",
            'created_at'  => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_reviews_user',     '{{%reviews}}', 'user_id',     '{{%users}}',     'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_reviews_provider', '{{%reviews}}', 'provider_id', '{{%providers}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_reviews_order',    '{{%reviews}}', 'order_id',    '{{%orders}}',    'id', 'CASCADE', 'CASCADE');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createNotificationsTable(): void
    {
        $this->createTable('{{%notifications}}', [
            'id'              => $this->bigPrimaryKey(),
            'user_id'         => $this->bigInteger()->notNull(),
            'type'            => $this->string(50)->notNull(),  // e.g. order_placed, payment_received
            'title'           => $this->string(255)->notNull(),
            'message'         => $this->text()->notNull(),
            'data'            => $this->json()->null(),          // JSON payload (order_id, etc.)
            'is_read'         => $this->boolean()->notNull()->defaultValue(false),
            'sent_via_sms'    => $this->boolean()->notNull()->defaultValue(false),
            'sent_via_email'  => $this->boolean()->notNull()->defaultValue(false),
            'created_at'      => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_notifications_user', '{{%notifications}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('idx_notifications_user_unread', '{{%notifications}}', ['user_id', 'is_read']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createFeaturedListingsTable(): void
    {
        $this->createTable('{{%featured_listings}}', [
            'id'          => $this->bigPrimaryKey(),
            'listing_id'  => $this->bigInteger()->notNull(),
            'provider_id' => $this->bigInteger()->notNull(),
            'start_date'  => $this->dateTime()->notNull(),
            'end_date'    => $this->dateTime()->notNull(),
            'amount_paid' => $this->decimal(10, 2)->notNull(),
            'status'      => "ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active'",
            'created_at'  => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_featured_listing',  '{{%featured_listings}}', 'listing_id',  '{{%listings}}',  'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_featured_provider', '{{%featured_listings}}', 'provider_id', '{{%providers}}', 'id', 'CASCADE', 'CASCADE');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function createSettingsTable(): void
    {
        $this->createTable('{{%settings}}', [
            'id'          => $this->primaryKey(),
            'key'         => $this->string(100)->notNull()->unique(),
            'value'       => $this->text()->null(),
            'description' => $this->string(500)->null(),
            'updated_at'  => $this->dateTime()->null(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function seedData(): void
    {
        // Subscription plans
        $this->batchInsert('{{%subscription_plans}}',
            ['name', 'slug', 'description', 'price_kes', 'duration_days', 'max_products', 'max_services', 'featured_slots', 'is_popular', 'status'],
            [
                ['Basic',        'basic',        'Great for solo service providers just getting started.', 1000, 30, 5,   3,   0, 0, 'active'],
                ['Professional', 'professional', 'Best value — ideal for growing businesses.',             2500, 30, 20,  10,  1, 1, 'active'],
                ['Premium',      'premium',      'Unlimited listings and maximum visibility.',             5000, 30, 999, 999, 3, 0, 'active'],
            ]
        );

        // Default categories
        $this->batchInsert('{{%categories}}',
            ['name', 'slug', 'icon', 'type', 'sort_order', 'status'],
            [
                ['Phone Repair',       'phone-repair',       'bi-phone',          'service', 1, 'active'],
                ['Plumbing',           'plumbing',           'bi-droplet',        'service', 2, 'active'],
                ['Electrical',         'electrical',         'bi-lightning',      'service', 3, 'active'],
                ['Cleaning',           'cleaning',           'bi-stars',          'service', 4, 'active'],
                ['IT Support',         'it-support',         'bi-laptop',         'service', 5, 'active'],
                ['Tutoring',           'tutoring',           'bi-book',           'service', 6, 'active'],
                ['Home Services',      'home-services',      'bi-house',          'service', 7, 'active'],
                ['Electronics',        'electronics',        'bi-cpu',            'product', 8, 'active'],
                ['Spare Parts',        'spare-parts',        'bi-tools',          'product', 9, 'active'],
                ['Household Items',    'household-items',    'bi-basket',         'product', 10, 'active'],
                ['Beauty & Wellness',  'beauty-wellness',    'bi-flower1',        'both',    11, 'active'],
                ['Auto Services',      'auto-services',      'bi-car-front',      'service', 12, 'active'],
            ]
        );

        // Default settings
        $this->batchInsert('{{%settings}}',
            ['key', 'value', 'description'],
            [
                ['commission_rate',       '10',        'Platform commission % on completed orders'],
                ['cod_max_amount',        '2000',      'Max order value (KES) allowed for payment on delivery'],
                ['deposit_max_amount',    '10000',     'Max order value (KES) requiring partial deposit'],
                ['deposit_percent',       '30',        'Deposit % of order total for partial payment'],
                ['subscription_grace_days', '3',       'Days of grace period after subscription expires'],
                ['kes_to_usd_rate',       '0.0077',    'Live KES to USD exchange rate (update via cron)'],
                ['platform_name',         'HudumaLynk','Platform display name'],
                ['support_phone',         '+254700000000', 'Platform support phone number'],
                ['support_email',         'support@hudumalynk.com', 'Platform support email'],
            ]
        );
    }
}
