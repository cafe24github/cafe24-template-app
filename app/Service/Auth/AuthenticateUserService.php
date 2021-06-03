<?php

namespace App\Service\Auth;

use App\Repository\Token\AccessTokenRepository;
use App\Service\BaseService;

/**
 * Class AuthenticateUserService
 * @package App\Service\Auth
 *
 * @author joven <joven@cafe24corp.com>
 * @version 1.0
 * @date 11/27/2020 10:16 AM
 */
class AuthenticateUserService extends BaseService
{
    /**
     * AuthenticateUserService constructor.
     * @param AccessTokenRepository  $oRepository
     */
    public function __construct(AccessTokenRepository $oRepository)
    {
        $this->oRepository = $oRepository;
    }

    /**
     * Pass access token to repository
     * @param array $aAccessToken
     * @return mixed
     */
    public function saveToken(Array $aAccessToken)
    {
        return $this->oRepository->saveAccessToken($aAccessToken);
    }
}
