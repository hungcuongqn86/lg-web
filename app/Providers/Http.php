<?php

namespace App\Providers;

use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Storage;

class Http
{
    private $cookiedir = 'cookie/curl-s.txt';
    private $storepath = '';

    function __construct()
    {
        $this->storepath = storage_path('app') . '/' . $this->cookiedir;
    }

    private function initCurl($url)
    {
        $ip = get_ip_address();
        return Curl::to($url)
            ->withHeader('X-Date: ' . gmdate("Ymd\THis\Z"))
            ->withHeader('X-Expires: 3600')
            ->withHeader('X-Remote-Addr: ' . $ip)
            ->withHeader('X-Authorization: WSS-HMAC-SHA256 Credential=30USDCOM/20170629/leadsgen/psp/wss-request,SignedHeaders=content-type;x-date;x-expires,Signature=1f655569edbcb36ef9e50b72b5658800b5893a5e2a7fa478d4a161350cf0d18f')
            ->withContentType('application/json')
            ->setCookieJar($this->storepath)
            ->setCookieFile($this->storepath);
    }

    /**
     *
     */
    private function setCookieBrowse()
    {
        if (Storage::disk('local')->has($this->cookiedir)) {
            // read the file
            $lines = file($this->storepath);
            foreach ($lines as $line) {
                // we only care for valid cookie def lines
                if ($line[0] != '#' && substr_count($line, "\t") == 6) {
                    // get tokens in an array
                    $tokens = explode("\t", $line);
                    // trim the tokens
                    $tokens = array_map('trim', $tokens);
                    // set to browse
                    if (!isset($_COOKIE[$tokens[5]])) {
                        setcookie($tokens[5], $tokens[6], $tokens[4], $tokens[2], $tokens[0]);
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    function get($url, $arrSearchParams = [], $bug = false)
    {
        $response = $this->initCurl($url);
        if ($arrSearchParams) {
            $response->withData($arrSearchParams);
        }
        if ($bug) {
            $logfile = 'logs/curl-' . date("Y-m-d-H-i-s") . '.log';
            $response->enableDebug(storage_path($logfile));
        } else {
            $response = $response->asJsonResponse()->get();
        }
        self::setCookieBrowse();
        return $response;
    }

    function post($url, $arrParams = [], $bug = false, $arrHeader = null)
    {
        $response = $this->initCurl($url);
        if ($arrHeader) {
            $response->withHeaders($arrHeader);
        }
        if ($arrParams) {
            $response->withData($arrParams);
        }
        if ($bug) {
            $logfile = 'logs/curl-' . date("Y-m-d-H-i-s") . '.log';
            $response->enableDebug(storage_path($logfile));
        } else {
            $response = $response->asJson()->post();
        }
        self::setCookieBrowse();
        return $response;
    }

    function put($url, $arrParams = [], $bug = false)
    {
        $response = $this->initCurl($url);
        if ($arrParams) {
            $response->withData($arrParams);
        }
        if ($bug) {
            $logfile = 'logs/curl-' . date("Y-m-d-H-i-s") . '.log';
            $response->enableDebug(storage_path($logfile));
        } else {
            $response = $response->asJson()->put();
        }
        self::setCookieBrowse();
        return $response;
    }
}