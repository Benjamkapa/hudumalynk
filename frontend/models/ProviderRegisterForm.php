<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Provider;

/**
 * ProviderRegisterForm — provider self-registration with business details
 */
class ProviderRegisterForm extends Model
{
    // Account fields
    public string $first_name       = '';
    public string $last_name        = '';
    public string $phone            = '';
    public string $email            = '';
    public string $password         = '';
    public string $password_confirm = '';

    // Business fields
    public string $business_name = '';
    public string $description   = '';
    public string $city          = 'Nairobi';
    public string $address       = '';
    public ?float $lat = null;
    public ?float $lng = null;

    // Uploaded ID document file instance
    public $id_document;

    public function rules(): array
    {
        return [
            [['first_name', 'last_name', 'phone', 'password', 'password_confirm', 'business_name', 'city'], 'required'],
            ['email',            'email'],
            ['email',            'unique', 'targetClass' => User::class, 'targetAttribute' => 'email'],
            ['phone',            'match', 'pattern' => '/^\+?[0-9]{9,15}$/'],
            ['phone',            'unique', 'targetClass' => User::class, 'targetAttribute' => 'phone',
                                 'message' => 'This phone is already registered.'],
            ['password',         'string', 'min' => 8],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.'],
            [['first_name', 'last_name'], 'string', 'max' => 100],
            ['business_name',    'string', 'max' => 255],
            ['description',      'string'],
            ['city',             'string', 'max' => 100],
            ['address',          'string'],
            [['lat', 'lng'], 'required', 'message' => 'Please select your business location on the map.'],
            ['lat', 'number', 'min' => -90, 'max' => 90],
            ['lng', 'number', 'min' => -180, 'max' => 180],
            ['id_document',      'file', 'skipOnEmpty' => true,
                                 'extensions' => 'jpg,jpeg,png,pdf',
                                 'maxSize'    => 5 * 1024 * 1024,
                                 'message'    => 'Please upload a valid ID (JPG, PNG or PDF, max 5MB).'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'first_name'    => 'Your First Name',
            'last_name'     => 'Your Last Name',
            'phone'         => 'Phone Number',
            'email'         => 'Email Address (optional)',
            'password'      => 'Password',
            'password_confirm' => 'Confirm Password',
            'business_name' => 'Business Name',
            'description'   => 'About Your Business',
            'city'          => 'City',
            'address'       => 'Business Address',
            'lat'           => 'Latitude',
            'lng'           => 'Longitude',
            'id_document'   => 'ID / Business Document',
        ];
    }

    public function register(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 1. Create user account
            $user             = new User();
            $user->first_name = $this->first_name;
            $user->last_name  = $this->last_name;
            $user->phone      = User::normalizePhone($this->phone);
            $user->email      = $this->email ?: null;
            $user->role       = User::ROLE_PROVIDER;
            $user->status     = User::STATUS_ACTIVE;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->save(false);

            // 2. Save ID document if uploaded
            $docPath = null;
            if ($this->id_document) {
                $uploadDir = Yii::getAlias('@uploads') . '/provider-docs/' . $user->id . '/';
                @mkdir($uploadDir, 0755, true);
                $fileName = 'id-doc.' . $this->id_document->extension;
                $this->id_document->saveAs($uploadDir . $fileName);
                $docPath = 'provider-docs/' . $user->id . '/' . $fileName;
            }

            // 3. Create provider profile
            $provider               = new Provider();
            $provider->user_id      = $user->id;
            $provider->business_name = $this->business_name;
            $provider->description  = $this->description;
            $provider->city         = $this->city;
            $provider->address      = $this->address;
            $provider->lat          = $this->lat;
            $provider->lng          = $this->lng;
            $provider->phone        = User::normalizePhone($this->phone);
            $provider->email        = $this->email ?: null;
            $provider->id_document  = $docPath;
            $provider->status       = Provider::STATUS_PENDING; // awaiting admin approval
            $provider->save(false);

            $transaction->commit();

            // 4. Notify admin of new provider registration
            // (admin notification via email — handled by admin panel listing)
            Yii::info('[Provider Registration] New provider: ' . $provider->business_name . ' (User #' . $user->id . ')', 'app');

            return $user;

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('[Provider Registration] Failed: ' . $e->getMessage(), 'app');
            return null;
        }
    }
}
