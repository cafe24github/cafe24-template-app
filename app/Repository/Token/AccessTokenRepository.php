<?php

namespace App\Repository\Token;

use App\Library\ConstantsLibrary;
use App\Repository\BaseRepository;

/**
 * Class AccessTokenRepository
 * @package App\Repository\Token
 *
 * @author joven <joven@cafe24corp.com>
 * @version 1.0
 * @date 12/1/2020 4:24 PM
 */
class AccessTokenRepository extends BaseRepository
{
    /**
     * Returns the access token from redis
     * Redis namespace format: app-name:mall-id:shop-no:access_token
     * @param $aParam
     * @return array
     */
    public function getAccessToken($aParam)
    {
        $sKey = sprintf(ConstantsLibrary::ACCESS_TOKEN_KEY, $aParam['mall_id'], $aParam['shop_no']);
        $this->initializeRedis($sKey);
        return $this->oRedis->getList()[0];
    }

    /**
     * Save Access Token to Redis
     * Redis namespace format: app-name:mall-id:shop-no:access_token
     * @param $aParam
     * @return int
     */
    public function saveAccessToken($aParam)
    {
        $sKey = sprintf(ConstantsLibrary::ACCESS_TOKEN_KEY, $aParam['mall_id'], $aParam['iShopNo']);
        $this->initializeRedis($sKey);
        $this->oRedis->delData();
        return $this->oRedis->insert($aParam);
    }
}
