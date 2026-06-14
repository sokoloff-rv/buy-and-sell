<?php

namespace app\components;

use yii\authclient\OAuth2;

class VKID extends OAuth2
{
    public $authUrl = 'https://id.vk.com/authorize';
    public $tokenUrl = 'https://id.vk.com/oauth2/auth';
    public $apiBaseUrl = 'https://id.vk.com/oauth2';
    public $enablePkce = true;
    public $scope = 'email';

    protected function initUserAttributes()
    {
        $response = $this->api('user_info', 'POST');

        return $response['user'] ?? $response;
    }

    protected function applyClientCredentialsToRequest($request)
    {
        $request->addData([
            'client_id' => $this->clientId,
        ]);
    }

    public function applyAccessTokenToRequest($request, $accessToken = null)
    {
        if ($accessToken === null) {
            $accessToken = $this->getAccessToken();
        }

        $request->addData([
            'client_id' => $this->clientId,
            'access_token' => $accessToken->getToken(),
        ]);
    }

    protected function defaultName()
    {
        return 'vkid';
    }

    protected function defaultTitle()
    {
        return 'VK ID';
    }
}
