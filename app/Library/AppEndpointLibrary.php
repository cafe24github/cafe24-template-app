<?php

namespace App\Library;

/**
 * Class AppEndpointLibrary
 * @package App\Library
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
Class AppEndpointLibrary
{
    // The following endpoints are app endpoints

    /**
     * Use to get page product data
     */
    const PAGE_DATA_PRODUCT = '/products/page';

    /*
     * Save product endpoint
     */
    const SAVE_PRODUCT = '/product/save';

    /**
     * Get saved product endpoint
     */
    const GET_SAVED_PRODUCT = '/products/saved';

    /**
     * Delete product endpoint
     */
    const DELETE_PRODUCT = '/product/%s';

    // The following endpoints includes cafe24 api call

    /**
     * Get scripttag status
     */
    const GET_SCRIPTTAG_STATUS = '/scripttag';

    /**
     * Toggle scripttag
     */
    const POST_SCRIPTTAG = '/scripttag';

    /**
     * Get categories endpoint
     */
    const GET_CATEGORY = '/categories';

    /**
     * Count products endpoint
     */
    const COUNT_PRODUCT = '/products/count';

    /**
     * Get products endpoint
     */
    const GET_PRODUCTS = '/products';

    /**
     * Get product with product number endpoint
     */
    const GET_PRODUCT = '/product/%s';

    /**
     * Get orders endpoint
     */
    const GET_ORDERS = '/orders';

    /**
     * Count orders endpoint
     */
    const COUNT_ORDERS = '/orders/count';

    /**
     * Get orders endpoint
     */
    const GET_COUNT_ORDERS = '/orders/page';


    /**
     * Get details of a specific order
     */
    const ORDER_DETAILS = '/order/%s';

    /**
     * Get products endpoint
     */
    const BUNDLE_PRODUCT = '/products/bundle';

}
