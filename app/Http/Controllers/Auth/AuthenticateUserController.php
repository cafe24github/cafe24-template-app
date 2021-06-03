<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Library\Cafe24\Provider\Cafe24;
use App\Service\Auth\AuthenticateUserService;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Redirect;

/**
 * Class AuthenticateUserController
 * @package App\Http\Controllers\Auth
 *
 * @author joven <joven@cafe24corp.com>
 * @version 1.0
 * @date 11/27/2020 10:16 AM
 */
class AuthenticateUserController extends Controller
{
    /**
     * @var $oRequest
     */
    private $oRequest;

    /**
     * AuthenticateUserController constructor.
     * @param AuthenticateUserService $oService
     * @param SessionManager          $oSession
     * @param Request                 $oRequest
     */
    public function __construct(AuthenticateUserService $oService, SessionManager $oSession, Request $oRequest)
    {
        $this->oService = $oService;
        $this->oSession = $oSession;
        $this->oRequest = $oRequest;
    }

    /**
     * Authenticate user and saves access token
     */
    public function authorizeMall()
    {
        $oParams = $this->oRequest->all();
        $sMallId = array_key_exists('state', $oParams) ? json_decode(base64_decode($oParams['state']))->mall_id : $oParams['mall_id'];
        $aOptions = [
            'clientId'                => config('auth.client_id'),
            'clientSecret'            => config('auth.client_secret'),
            'redirectUri'             => config('auth.redirect_uri'),
            'mall_id'                 => $sMallId
        ];

        //Create an instance of the Cafe24 OAuth 2.0 Provider
        $oCafe24AuthProvider = new Cafe24($aOptions);

        // Get authorization code
        if (!isset($oParams['code'])) {
            // Options are optional, defaults to 'profile' only
            $options = [
                'state' => base64_encode(json_encode($oParams)),
                'scope' => explode(',', config('auth.scope'))
            ];

            // Get authorization URL
            $authorizationUrl = $oCafe24AuthProvider->getAuthorizationUrl($options);

            // Get state and store it to the session
            $this->oSession->put('oauth2state', $oCafe24AuthProvider->getState());

            // Redirect user to authorization URL
            return Redirect::to($authorizationUrl);
            // Check for errors
        } elseif (empty($oParams['state']) || ($this->oSession->get('oauth2state') !== null && $oParams['state'] !== $this->oSession->get('oauth2state'))) {
            if ($this->oSession->get('oauth2state') !== null) {
                $this->oSession->forget('oauth2state');
            }

            return redirect()->route('forbidden');
        } else {
            $aParams = json_decode(base64_decode($oParams['state']), true);
            $this->oSession->put('shop_no', $aParams['shop_no']);
            $this->oSession->put('mall_id', $aParams['mall_id']);
            $this->oSession->put('user_id', $aParams['user_id']);
            $this->oSession->put('user_type', $aParams['user_type']);
            // Get access token
            try {
                //get access token
                $accessToken = $oCafe24AuthProvider->getAccessToken(
                    'authorization_code',
                    [
                        'code' => $oParams['code']
                    ]
                );

                //save access token
                $aAccessToken = $accessToken->jsonSerialize();
                $aAccessToken['iShopNo'] = $this->oSession->get('shop_no');
                $this->oService->saveToken($aAccessToken);
                return redirect('/dashboard');

                //test refresh token
                //$refreshToken = $oCafe24AuthProvider->getAccessToken(
                //    'refresh_token',
                //    [
                //        'refresh_token' => $accessToken->getRefreshToken()
                //    ]
                //);


                //test and get the mall details
                //$mall_details = $oCafe24AuthProvider->getResourceOwner($refreshToken);

                // $mall_details->getId(), $mall_details->toArray() to get the mall details
                die();
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                exit($e->getMessage());
            }
        }
    }
}
