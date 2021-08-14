<?php
declare(strict_types=1);

namespace LemonMarketsClient;

use GuzzleHttp\Client as GuzzeClient;
use JMS\Serializer\Serializer as JsonSerializer;
use LemonMarketsClient\Model\Response\AccessToken;

class TokenClient extends AbstractClient
{
    public function __construct(
        private string  $clientId,
        private string  $secret,
        ?GuzzeClient    $httpClient = null,
        ?JsonSerializer $serializer = null,
    ) {
        parent::__construct($httpClient, $serializer);
    }

    public function authenticate(): AccessToken
    {
        $response = $this->httpClient->post('https://auth.lemon.markets/oauth2/token', [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->secret,
                'grant_type' => 'client_credentials',
            ],
        ]);

        $body = (string) $response->getBody();

        return $this->deserialize($body, AccessToken::class);
    }
}
