<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * User model — central identity for all roles
 *
 * @property int    $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $password_hash
 * @property string $auth_key
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $role
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE    = 'active';
    const STATUS_INACTIVE  = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    const ROLE_CUSTOMER = 'customer';
    const ROLE_PROVIDER = 'provider';
    const ROLE_ADMIN    = 'admin';

    const SCENARIO_REGISTER         = 'register';
    const SCENARIO_REGISTER_PROVIDER = 'register_provider';
    const SCENARIO_UPDATE           = 'update';
    const SCENARIO_CHANGE_PASSWORD  = 'change_password';

    /** @var string Plain-text password (not stored) */
    public $password;
    public $password_confirm;
    public $current_password;

    public static function tableName(): string
    {
        return '{{%users}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class'               => TimestampBehavior::class,
                'createdAtAttribute'  => 'created_at',
                'updatedAtAttribute'  => 'updated_at',
                'value'               => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            // Required fields
            [['first_name', 'last_name', 'phone'], 'required'],
            [['email'], 'required', 'except' => self::SCENARIO_REGISTER_PROVIDER],

            // Password required on registration
            [['password', 'password_confirm'], 'required',
                'on' => [self::SCENARIO_REGISTER, self::SCENARIO_REGISTER_PROVIDER]],
            ['password_confirm', 'compare', 'compareAttribute' => 'password',
                'message' => 'Passwords do not match.'],
            ['password', 'string', 'min' => 8,
                'on' => [self::SCENARIO_REGISTER, self::SCENARIO_REGISTER_PROVIDER, self::SCENARIO_CHANGE_PASSWORD]],

            // Formats
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::class,
                'filter' => function ($query) { $query->andWhere(['!=', 'id', $this->id ?? 0]); }],
            ['phone', 'match', 'pattern' => '/^\+?[0-9]{9,15}$/',
                'message' => 'Enter a valid phone number (e.g. +254712345678).'],
            ['phone', 'unique', 'targetClass' => self::class,
                'filter' => function ($query) { $query->andWhere(['!=', 'id', $this->id ?? 0]); }],

            // Enums
            ['role',   'in', 'range' => [self::ROLE_CUSTOMER, self::ROLE_PROVIDER, self::ROLE_ADMIN]],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_SUSPENDED]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],

            // Lengths
            [['first_name', 'last_name'], 'string', 'max' => 100],
            ['email', 'string', 'max' => 150],
            ['phone', 'string', 'max' => 20],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'               => 'ID',
            'first_name'       => 'First Name',
            'last_name'        => 'Last Name',
            'email'            => 'Email Address',
            'phone'            => 'Phone Number',
            'password'         => 'Password',
            'password_confirm' => 'Confirm Password',
            'role'             => 'Role',
            'status'           => 'Status',
            'created_at'       => 'Member Since',
        ];
    }

    // ── Role helpers ─────────────────────────────────────────────────────────

    public function isAdmin(): bool    { return $this->role === self::ROLE_ADMIN; }
    public function isProvider(): bool { return $this->role === self::ROLE_PROVIDER; }
    public function isCustomer(): bool { return $this->role === self::ROLE_CUSTOMER; }
    public function isActive(): bool   { return $this->status === self::STATUS_ACTIVE; }

    public function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getInitials(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    // ── Password ─────────────────────────────────────────────────────────────

    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }

    public function generateEmailVerificationToken(): void
    {
        $this->verification_token = Yii::$app->security->generateRandomString();
    }

    public static function isPasswordResetTokenValid(string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire    = 3600; // 1 hour
        return $timestamp + $expire >= time();
    }

    // ── Finders ──────────────────────────────────────────────────────────────

    public static function findByEmailOrPhone(string $identifier): ?self
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['or', ['email' => $identifier], ['phone' => $identifier]])
            ->one();
    }

    public static function findByPasswordResetToken(string $token): ?self
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne(['password_reset_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    // ── IdentityInterface ─────────────────────────────────────────────────────

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /** @throws NotSupportedException */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Token-based auth is not supported.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    // ── Unread notifications count ────────────────────────────────────────────

    public function getUnreadNotificationsCount(): int
    {
        return (int) Notification::find()
            ->where(['user_id' => $this->id, 'is_read' => false])
            ->count();
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function getProvider()
    {
        return $this->hasOne(Provider::class, ['user_id' => 'id']);
    }

    public function getOrders()
    {
        return $this->hasMany(Order::class, ['user_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    public function getReviews()
    {
        return $this->hasMany(Review::class, ['user_id' => 'id']);
    }

    public function getNotifications()
    {
        return $this->hasMany(Notification::class, ['user_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    // ── Phone normalizer ──────────────────────────────────────────────────────

    /** Converts various formats (07..., +254...) → 2547XXXXXXXX */
    public static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        if (str_starts_with($phone, '+')) {
            return ltrim($phone, '+');
        }
        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }
        return $phone;
    }
}
