<?php

namespace App\Service\App;

use App\Library\AppEndpointLibrary;
use App\Library\ConstantsLibrary;
use App\Library\GuzzleLibrary;
use App\Service\BaseService;
/**
 * Class CategoryService
 * @package App\Service\Auth
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class CategoryService extends BaseService
{
    /**
     * Instance for blGuzzle class
     *
     * @var GuzzleLibrary $oBlGuzzle
     */
    private $oGuzzleLibrary;

    /**
     * CategoryService constructor.
     * @param GuzzleLibrary $oGuzzleLibrary
     */
    public function __construct(GuzzleLibrary $oGuzzleLibrary)
    {
        $this->oGuzzleLibrary = $oGuzzleLibrary;
    }

    /**
     * Use to get Categories
     * @return mixed
     */
    public function getCategories()
    {
        $this->setRedisParams();
        return $this->oGuzzleLibrary->setEndpoint(AppEndpointLibrary::GET_CATEGORY)->guzzleApi($this->oRedisParams);
    }
}
