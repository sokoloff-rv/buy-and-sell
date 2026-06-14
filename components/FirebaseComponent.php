<?php

namespace app\components;

use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Factory;
use yii\base\Component;
use yii\base\InvalidConfigException;

class FirebaseComponent extends Component
{
    public string $credentialsPath = '';
    public string $databaseUri = '';

    private ?Auth $auth = null;
    private ?Database $database = null;

    public function getAuth(): Auth
    {
        return $this->auth ??= $this->createFactory()->createAuth();
    }

    public function getDatabase(): Database
    {
        return $this->database ??= $this->createFactory()->createDatabase();
    }

    public function createCustomToken(int $userId): string
    {
        return $this->getAuth()->createCustomToken((string) $userId, [], 3600)->toString();
    }

    private function createFactory(): Factory
    {
        if ($this->credentialsPath === '' || !is_file($this->credentialsPath)) {
            throw new InvalidConfigException('Firebase service account JSON не найден.');
        }
        if ($this->databaseUri === '') {
            throw new InvalidConfigException('Firebase Realtime Database URI не настроен.');
        }

        return (new Factory())
            ->withServiceAccount($this->credentialsPath)
            ->withDatabaseUri($this->databaseUri);
    }
}
