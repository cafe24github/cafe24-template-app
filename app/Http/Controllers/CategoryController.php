<?php

namespace App\Http\Controllers;

use App\Library\ConstantsLibrary;
use App\Service\App\CategoryService;
use App\Service\App\ProductService;
use Illuminate\Http\Request;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class CategoryController extends BaseController
{
    /**
     * @var CategoryService
     */
    private $oCategoryService;

    /**
     * CategoryController constructor.
     * @param CategoryService $oCategoryService
     */
    public function __construct(CategoryService $oCategoryService)
    {
        $this->oCategoryService = $oCategoryService;
    }

    /**
     * Use to get Categories
     * @param Request $aRequest
     * @return mixed
     */
    public function getCategories(Request $aRequest)
    {
        return $this->oCategoryService->getCategories();
    }
}
