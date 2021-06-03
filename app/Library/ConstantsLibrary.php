<?php

namespace App\Library;

/**
 * Interface ConstantsLibrary
 * @package App\Library
 */
interface ConstantsLibrary
{
    /**
     * route version
     * @const ROUTE_VERSION
     */
    const ROUTE_VERSION = '/v1/';

    /**
     * regex for valid link
     * @const constant REGEX_VALID_LINK
     */
    const REGEX_VALID_LINK = '/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&\/=]*)/';

    /**
     * image prefix
     * @const constant APP_NAME
     */
    const APP_NAME = 'cafe24_template_app';

    /**
     * Access token literal
     * @constant ACCESS_TOKEN
     */
    const ACCESS_TOKEN = ':access_token';

    /**
     * mall ID
     * @const MALL_ID
     */
    const MALL_ID = 'mall_id';

    /**
     * zero index
     * @const ZERO_INDEX
     */
    const ZERO_INDEX = 0;

    /**
     * front origin
     * @const FRONT_ORIGIN
     */
    const FRONT_ORIGIN = 'front';

    /**
     * admin origin
     * @const ADMIN_ORIGIN
     */
    const ADMIN_ORIGIN = 'admin';

    /**
     * front js
     * @const FRONT_MODULE
     */
    const FRONT_MODULE = '/buildFrontJs';

    /**
     * location type
     * @const DEFAULT_SELECTOR
     */
    const DEFAULT_SELECTOR = 'none';

    /**
     * shop key
     * @const SHOP_KEY
     */
    const SHOP_NO = 'shop_no';

    /**
     * User id key
     * @const SHOP_KEY
     */
    const USER_ID = 'user_id';

    /**
     * Default image to display
     */
    const PROD_IMAGE_DEFAULT = '//img.echosting.cafe24.com/thumb/104x104_1.gif';

    /**
     * Page Key
     */
    const PAGE = 'page';

    /**
     * Default limit
     */
    const DEFAULT_LIMIT = 10;

    /**
     * Get key
     */
    const GET = 'GET';

    /**
     * Post key
     */
    const POST = 'POST';

    /**
     * Delete key
     */
    const DELETE = 'DELETE';

    /**
     * Refresh token key
     */
    const REFRESH_KEY = 'refresh_token';

    /**
     * Default value when product count is 0
     */
    const EMPTY_PRODUCT = [ 'products' => [] ];

    /**
     * Error code returned by the API if access token is expired
     */
    const API_EXPIRED_TOKEN_CODE = 401;

    /**
     * Api unprocessable code
     */
    const API_UNPROCESSABLE_CODE = 422;

    /**
     * Message when api really failed
     */
    const API_ERROR_MESSAGE = 'Something went wrong';

    /**
     * Message when product number is invalid
     */
    const INV_PROD_NUM_MESSAGE = 'Invalid Product Number';

    /**
     * Message if product number do not exist
     */
    const PROD_NO_NOT_EXIST_MESSAGE = 'Product Number do not exist';

    /**

     * Defaul display and selling status
     * @const DISPLAY_SELLING
     */
    const DISPLAY_SELLING = '&display=T&selling=T&';

    /**
     * Message if product is existing and trying to be saved again
     */
    const EXISTING_PRODUCT_MESSAGE = 'Product Already saved';

    /**
     * Message if product reach its maximum capacity
     */
    const MAX_PRODUCT_MESSAGE = 'Product saved reach it\'s maximum';

    /**
     * Message when product is added successfully
     */
    const PRODUCT_ADDED_MESSAGE = 'Product is added';

    /**
     * Message when product is removed successfully
     */
    const PRODUCT_REMOVED_MESSAGE = 'Product is removed';
    /**
     * Message when trying to delete a product that is not saved
     */
    const PRODUCT_NOT_SAVED_MESSAGE = 'Product is not in the saved list';

    /**
     * Data key
     */
    const DATA = 'data';

    /**
     * Count key
     */
    const COUNT = 'count';

    /**
     * Default Validator
     */
    const DEFAULT_VALIDATOR = [
        'mall_id'        => ['required', 'string'],
        'shop_no'        => ['required', 'numeric', 'min:1'],
    ];

    /**
     * Script tag redis namespace
     */
    const SCRIPT_TAG_KEY = 'cafe24-template-app:%s:shop%s:scripttag';

    /**
     * Product redis namespace
     */
    const PRODUCT_KEY = 'cafe24-template-app:%s:shop%s:product';

    /**
     * Product cache redis namespace
     */
    const PRODUCT_CACHE_KEY = 'cafe24-template-app:%s:shop%s:product:cstore';

    /**
     * Access token redis namespace
     */
    const ACCESS_TOKEN_KEY = 'cafe24-template-app:%s:shop%s:access_token';

    /** Error message when order ID is missing */
    const ORDER_ID_ERROR = 'Order ID must be required';

    /** API Code that needs api recall*/
    const RETRY_API_ERROR_CODE = [429, 500, 503];

    /** Error messsage that randomly happens while installing the
     * scripttag
     */
    const CORS_ERROR_MESSAGE = 'The CORS (Cross-origin resource sharing) header of the script in the src attribute must be \"Access-Control-Allow-Origin: *\". (parameter.src)';
}
