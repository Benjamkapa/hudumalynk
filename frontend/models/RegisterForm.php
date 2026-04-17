<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * RegisterForm — customer self-registration form
 */
class RegisterForm extends Model
{
    public string $first_name       = '';
    public string $last_name        = '';
    public string $email            = '';
    public string $phone            = '';
    public string $password         = '';
    public string $password_confirm = '';

    public function rules(): array
    {
        return [
            [['first_name', 'last_name', 'email', 'phone', 'password', 'password_confirm'], 'required'],
            ['email',            'email'],
            ['email',            'unique', 'targetClass' => User::class, 'targetAttribute' => 'email',
                                 'message' => 'This email is already registered.'],
            ['phone',            'match', 'pattern' => '/^\+?[0-9]{9,15}$/',
                                 'message' => 'Enter a valid phone (e.g. 0712345678 or +254712345678).'],
            ['phone',            'unique', 'targetClass' => User::class, 'targetAttribute' => 'phone',
                                 'message' => 'This phone number is already registered.'],
            ['password',         'string', 'min' => 8, 'message' => 'Password must be at least 8 characters.'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.'],
            [['first_name', 'last_name'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'first_name'       => 'First Name',
            'last_name'        => 'Last Name',
            'email'            => 'Email Address',
            'phone'            => 'Phone Number',
            'password'         => 'Password',
            'password_confirm' => 'Confirm Password',
        ];
    }

    public function register(): ?User
    {
        if (!$this->validate()) {
            return null;
        }
        $user             = new User();
        $user->first_name = $this->first_name;
        $user->last_name  = $this->last_name;
        $user->email      = $this->email;
        $user->phone      = User::normalizePhone($this->phone);
        $user->role       = User::ROLE_CUSTOMER;
        $user->status     = User::STATUS_ACTIVE;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        if ($user->save(false)) {
            Yii::$app->sms->notify($user, \common\models\Notification::TYPE_GENERAL, [
                'message' => 'Welcome to HudumaLynk, ' . $user->first_name . '! Start exploring services and products near you.',
            ], true, true);
            return $user;
        }
        return null;
    }
}
