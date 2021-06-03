<?php

namespace App\Library\Cafe24\Provider;

use http\Exception\InvalidArgumentException;
use League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider;
use League\OAuth2\Client\OptionProvider\OptionProviderInterface;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;

/**
 * OAuth2 Provider for Cafe24 API
 *
 * @author john03@cafe24corp.com
 * @since 2020/11/04
 * @package Cafe24ph\OAuth2\Client\Provider
 */
class Cafe24 extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * Mall ID
     * @var string
     */
    protected $mall_id;

    /**
     * Cafe24 API domain prefix
     * @var string
     */
    const CAFE24_API_DOMAIN = 'https://%s.cafe24api.com/api';

    /**
     * Name of the resource owner identifier field that is
     * present in the access token response (if applicable)
     */
    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'user_id';


    /**
     * Override to set expiration of the access token
     * @param array $result
     * @return array
     */
    protected function prepareAccessTokenResponse(array $result)
    {
        $result['expires_in'] = strtotime($result['expires_at']) - time();

        return parent::prepareAccessTokenResponse($result);
    }

    /**
     * Override default option provider
     * @param OptionProviderInterface $provider
     * @return $this|AbstractProvider
     */
    public function setOptionProvider(OptionProviderInterface $provider)
    {
        $this->optionProvider = new HttpBasicAuthOptionProvider();

        return $this;
    }

    /**
     * This function will return the URL that would be used to authorize the user
     * @inheritDoc
     */
    public function getBaseAuthorizationUrl()
    {
        return sprintf(self::CAFE24_API_DOMAIN . '/v2/oauth/authorize', $this->mall_id);
    }

    /**
     * This function will return the URL that would be used to get an access token after authorizing the user
     * @inheritDoc
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return sprintf(self::CAFE24_API_DOMAIN . '/v2/oauth/token', $this->mall_id);
    }

    /**
     * This function will return the URL that would be used to get the basic information of the user
     * @inheritDoc
     */
    public function getResourceOwnerDetailsUrl(\League\OAuth2\Client\Token\AccessToken $token)
    {
        return sprintf(self::CAFE24_API_DOMAIN . '/v2/admin/store', $this->mall_id);
    }

    /**
     * This function will return the default scopes which would be used if there are no scopes provided
     * @inheritDoc
     */
    protected function getDefaultScopes()
    {
        return [
            'mall.read_application',
            'mall.write_application',
        ];
    }

    /**
     * This function checks if there is an error with the access token
     * @inheritDoc
     */
    protected function checkResponse(\Psr\Http\Message\ResponseInterface $response, $data)
    {
        if (array_key_exists('error', $data) === true) {
            $statusCode = $response->getStatusCode();
            throw new IdentityProviderException(
                $statusCode . ' - ' . json_encode($data),
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * This function will create a Cafe24ResourceOwner class which contains the basic information of the user
     * @inheritDoc
     */
    protected function createResourceOwner(array $response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return new Cafe24ResourceOwner($response);
    }
}
