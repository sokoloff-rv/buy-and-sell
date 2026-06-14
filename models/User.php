<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'email', 'password', 'avatar'], 'string', 'max' => 255],
            [['vk_id'], 'integer'],
            [['email'], 'unique'],
            [['vk_id'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя и фамилия',
            'email' => 'Email',
            'password' => 'Пароль',
            'avatar' => 'Аватар',
            'vk_id' => 'Vk ID',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    public function getComments()
    {
        return $this->hasMany(Comment::class, ['user_id' => 'id']);
    }

    public function getOffers()
    {
        return $this->hasMany(Offer::class, ['user_id' => 'id']);
    }

    public static function findIdentity($id): ?User
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?User
    {
        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthKey(): ?string
    {
        return null;
    }

    public function validateAuthKey($authKey): bool
    {
        return false;
    }

    public function createUserFromVK($userData)
    {
        $user = new User;
        $user->name = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
        $user->email = $userData['email'] ?? null;

        $user->password = null;
        $user->vk_id = $userData['user_id'];
        $user->avatar = $userData['avatar'] ?? null;
        if (!$user->save()) {
            throw new \RuntimeException('Не удалось зарегистрировать пользователя VK.');
        }

        self::assignUserRole($user->id);
        Yii::$app->user->login($user);
    }

    public static function assignUserRole(int $userId): void
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('user');
        if ($role && !$auth->getAssignment('user', (string) $userId)) {
            $auth->assign($role, $userId);
        }
    }

    public function getAvatarUrl(): string
    {
        if (!$this->avatar) {
            return '/img/avatar.jpg';
        }
        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://') || str_starts_with($this->avatar, '/')) {
            return $this->avatar;
        }

        return '/' . $this->avatar;
    }
}
