<?php

namespace App\Library\Cafe24\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Class for getting the main shop details of the authenticated mall
 *
 * @author john03@cafe24corp.com
 * @since 2020/11/04
 * @package Cafe24ph\OAuth2\Client\Provider
 */
class Cafe24ResourceOwner implements ResourceOwnerInterface
{
    /**
     * The response data from the API
     * @var array
     */
    private $response;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response['store'];
    }

    /**
     * This will get the ID of the user, in this case, the mall_id
     * @inheritDoc
     */
    public function getId()
    {
        return $this->response['mall_id'];
    }

    /**
     * This function returns the whole response data from the API
     * This assumes that the response is an object type, but since V2X API already returns an array, there is no need to parse it
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->response;
    }
}
