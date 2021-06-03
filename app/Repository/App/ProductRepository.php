<?php

namespace App\Repository\App;

use App\Library\ConstantsLibrary;
use App\Repository\BaseRepository;

/**
 * Class ProductRepository
 * @package App\Repository\Token
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class ProductRepository extends BaseRepository
{
    /**
     * Product number key
     */
    const PRODUCT_NO = 'product_no';

    /**
     * Product Key
     */
    const PRODUCT = 'product';

    /**
     * Endpoint Key
     */
    const ENDPOINT = 'endpoint';

    /**
     * Method Key
     */
    const METHOD = 'method';

    /**
     * Default ttl for redis cache
     */
    const TTL = 300;

    /**
     * Use to save product
     * Redis namespace format: app-name:mall-id:shop-no:product
     * @param $aParam
     * @return int
     */
    public function saveProduct($aParam)
    {
        $sKey = sprintf(ConstantsLibrary::PRODUCT_KEY, $aParam['mall_id'], $aParam['shop_no']);
        $this->initializeRedis($sKey);
        return $this->oRedis->insert($aParam[self::PRODUCT]);
    }

    /**
     * Use to save product
     * Redis namespace format: app-name:mall-id:shop-no:product:cstore
     * @param $aParam
     * @return int
     */
    public function saveApiReturn($aParam)
    {
        $sKey = sprintf(ConstantsLibrary::PRODUCT_CACHE_KEY, $aParam['mall_id'], $aParam['shop_no']);
        $this->initializeRedis($sKey);
        $this->oRedis->setExpire(self::TTL);
        return $this->oRedis->insert($aParam);
    }

    /**
     * Get cached api return
     * Redis namespace format: app-name:mall-id:shop-no:product:cstore
     * @param $aParam
     * @return bool|int|mixed
     */
    public function getApiReturn($aParam)
    {
        $sKey = sprintf(ConstantsLibrary::PRODUCT_CACHE_KEY, $aParam['mall_id'], $aParam['shop_no']);
        $this->initializeRedis($sKey);
        $mIndex = $this->oRedis->getIndex([
            self::ENDPOINT => $aParam[self::ENDPOINT],
            self::METHOD   => $aParam[self::METHOD]
        ]);

        return is_numeric($mIndex) === false ? false : $this->oRedis->getIndexData($mIndex);
    }

    /**
     * Use to get product by number
     * Redis namespace format: app-name:mall-id:shop-no:product
     * @param $aParam
     * @return bool|int
     */
    public function getProductByNo($aParam)
    {
        $sKey = sprintf(ConstantsLibrary::PRODUCT_KEY, $aParam['mall_id'], $aParam['shop_no']);
        $this->initializeRedis($sKey);
        return $this->oRedis->getIndex([self::PRODUCT_NO => (int) $aParam[self::PRODUCT_NO]]);
    }

    /**
     * Use to get Saved product
     * Redis namespace format: app-name:mall-id:shop-no:product
     * @param $aParam
     * @return array
     */
    public function getSavedProduct($aParam)
    {
        $sKey = sprintf(ConstantsLibrary::PRODUCT_KEY, $aParam['mall_id'], $aParam['shop_no']);
        $this->initializeRedis($sKey);
        return $this->oRedis->getAllList();
    }

    /**
     * Use to count saved product in db
     * Redis namespace format: app-name:mall-id:shop-no:product
     * @param $aParam
     * @return int
     */
    public function countSavedProduct($aParam)
    {
        $sKey = sprintf(ConstantsLibrary::PRODUCT_KEY, $aParam['mall_id'], $aParam['shop_no']);
        $this->initializeRedis($sKey);
        return $this->oRedis->getListLength();
    }

    /**
     * Use to delete product
     * Redis namespace format: app-name:mall-id:shop-no:product
     * @param $iProductNo
     * @param $aParam
     * @return array
     */
    public function deleteProduct($iProductNo, $aParam)
    {
        $sKey = sprintf(ConstantsLibrary::PRODUCT_KEY, $aParam['mall_id'], $aParam['shop_no']);
        $this->initializeRedis($sKey);
        return $this->oRedis->removeByFilter([self::PRODUCT_NO => $iProductNo]);
    }

    /**
     * Use to check if script is already save in redis
     * Redis namespace format: app-name:mall-id:shop-no:scripttag
     * @param $aParam
     * @return array
     */
    public function checkScriptInstallable($aParam)
    {
        $sKey = sprintf(ConstantsLibrary::SCRIPT_TAG_KEY, $aParam['mall_id'], $aParam['shop_no']);
        $this->initializeRedis($sKey);
        return $this->oRedis->getAllList();
    }

    /**
     * Save scripttag in redis
     * Redis namespace format: app-name:mall-id:shop-no:scripttag
     * @param $aParam
     * @return int
     */
    public function saveScriptTag($aParam)
    {
        $sKey = sprintf(ConstantsLibrary::SCRIPT_TAG_KEY, $aParam['mall_id'], $aParam['shop_no']);
        $this->initializeRedis($sKey);
        $this->oRedis->delData();
        return $this->oRedis->insert($aParam['scripttag']);
    }
}
