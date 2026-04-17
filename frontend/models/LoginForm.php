<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * LoginForm — handles login via email OR phone + password
 */
class LoginForm extends Model
{
    public string $identifier = '';  // email or phone
    public string $password   = '';
    public bool   $rememberMe = true;

    private ?User $_user = null;

    public function rules(): array
    {
        return [
            [['identifier', 'password'], 'required'],
            ['identifier', 'string', 'max' => 150],
            ['password',   'string', 'max' => 255],
            ['rememberMe', 'boolean'],
            ['password',   'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'identifier' => 'Email or Phone',
            'password'   => 'Password',
            'rememberMe' => 'Keep me signed in',
        ];
    }

    public function validatePassword($attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect email/phone or password.');
            }
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmailOrPhone($this->identifier);
        }
        return $this->_user;
    }
}
