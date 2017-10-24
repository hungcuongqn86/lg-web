<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    public function __construct(Request $request)
    {
        if ($request->ajax()) {
            return true;
        }

        $trackPrefix = config('config.analyticsPrefix');
        $cookieDie = time() + (1 * 24 * 60 * 60);

        $arrurl = parse_url($request->fullUrl());
        if (!isset($arrurl['port'])) {
            $arrurl['port'] = '80';
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $refData = parse_url($_SERVER['HTTP_REFERER']);
            if (!isset($refData['port'])) {
                $refData['port'] = '80';
            }
            if (($refData['host'] === $arrurl['host']) && ($refData['port'] === $arrurl['port'])) {
                // Check cookie
                if (isset($_COOKIE[$trackPrefix])) {
                    $analstr = $_COOKIE[$trackPrefix];
                    $arrAnal = json_decode($analstr, true);
                    $arrTra = $arrAnal;
                } else {
                    $arrTra['source'] = 'marketplace';
                }
            } else {
                if (strrpos($refData['host'], 'facebook')) {
                    $arrTra['source'] = 'facebook';
                } else if (strrpos($refData['host'], 'google')) {
                    $arrTra['source'] = 'google';
                } else {
                    $arrTra['source'] = $refData['host'];
                }
            }
        } else {
            $arrTra['source'] = 'direct';
        }
        $arrTra['medium'] = isset($_GET["utm_medium"]) ? $_GET["utm_medium"] : '';
        $arrTra['campaign'] = isset($_GET["utm_campaign"]) ? $_GET["utm_campaign"] : '';
        $arrTra['content'] = isset($_GET["utm_content"]) ? $_GET["utm_content"] : '';
        $arrTra['url'] = $request->fullUrl();
        setcookie($trackPrefix, json_encode($arrTra), $cookieDie, '/');
    }
}
