<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Http;
use Illuminate\Support\Facades\Redis;


class HomeController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        //Get category list
        $category_list = redis_category();

        //Get order number
        $order_numb = redis_order_number();

        //Best seller
        if (Redis::exists(url()->current() . "/shop/best-sellers?page=1")) {
            $campaign_bestseller = Redis::get(url()->current() . "/shop/best-sellers?page=1");
            $campaign_bestseller = json_decode($campaign_bestseller)->data;
        } else {
            //Hard code category id
            $cat_bestseller = 'v0NfauK7DTMXYpSE';
            $campaign_bestseller = Http::get(config('config.pspApiPath') . '/campaigns', ['state' => 'launching', 'categories' => $cat_bestseller, 'page' => 1, 'page_size' => 12]);
            $total = $campaign_bestseller->total;
            $campaign_bestseller = extendCampaignObj($campaign_bestseller->campaigns);
            $campaign_data = array("total" => $total,
                "data" => $campaign_bestseller);

            Redis::set(url()->current() . "/shop/best-sellers?page=1", json_encode($campaign_data));
            Redis::expire(url()->current() . "/shop/best-sellers?page=1", 60 * 10);
        }

        //Get maximum 8 campaigns
        if (count($campaign_bestseller) > 8) $campaign_bestseller = array_splice($campaign_bestseller, 0, 8);

        //Recently viewed
        $recently_view_data = null;
        if (isset($_COOKIE["recently_viewed"])) {
            $recently_view = json_decode($_COOKIE["recently_viewed"]);
            if ($recently_view && count($recently_view)) {
                foreach ($recently_view as $key => $item) {
                    if (Redis::exists($item->url)) {
                        $view_data = Redis::get($item->url);
                        $view_data = json_decode($view_data);

                        $recently_view_data[] = $view_data;
                        if (count($recently_view_data) == 8) break;

                    }
                }
            }
        }

        $this->data['pagejs'] = genHtmlLoadFile(['js/shop/home.js']);
        $this->data['campaign_bestseller'] = $campaign_bestseller;
        $this->data['recently_view'] = $recently_view_data;
        $this->data['category'] = $category_list;
        $this->data['orderno'] = $order_numb;
        $this->data['site'] = 'home';
        return view('shop::home', $this->data);
    }

    /**
     * Display a track page.
     * @return Response
     */
    public function track()
    {
        //Get category list
        $category_list = redis_category();
        //Get order number
        $order_numb = redis_order_number();

        $error_lookup = 0;
        if (isset($_GET["pid"]) && strlen($_GET["pid"])) {
            $order = Http::get(config('config.pspApiPath') . '/orders_tracking?code=' . $_GET["pid"]);
            if (isset($order->id) && isset($order->items) && count($order->items)) {
                //Get total
                $total_quantity = 0;
                $total_price = 0;
                $total_shipping = 0;
                foreach ($order->items as $key => $item) {
                    $total_quantity += $item->quantity;
                    $total_price += $item->quantity * $item->price;
                    $total_shipping += $item->shipping_fee;
                    $order->items[$key]->price = number_format($item->price, 2);
                    $order->items[$key]->shipping_fee = number_format($item->shipping_fee, 2);
                }
                $order->total_quantity = $total_quantity;
                $order->total_price = number_format($total_price, 2);
                $order->total_shipping = number_format($total_shipping, 2);
                $order->amount = number_format($order->amount, 2);
                if (isset($order->payment)) {
                    $order->payment->pay_time = date('l, M j', strtotime($order->payment->pay_time));
                }

                $chk_same_campaign = 1;
                $campaign_url = $order->items[0]->campaign_url;
                foreach ($order->items as $item) {
                    if ($item->campaign_url != $campaign_url) {
                        $chk_same_campaign = 0;
                        break;
                    }
                }

                $campaign_title = '';
                $campaign_image = '';
                $date_end = '';
                if ($chk_same_campaign) {
                    //Get end campaign
                    $campaign_selected = Http::get(config('config.pspApiPath') . '/campaigns', ['url' => substr($campaign_url, 1)]);
                    if ((array)$campaign_selected) {
                        $campaign_selected = extendCampaignObj(array($campaign_selected))[0];
                        $campaign_title = $campaign_selected->title;
                        $date_end = date("M jS - Y", strtotime($campaign_selected->end_time));
                        $campaign_image = $campaign_selected->image;
                    }
                }
                $this->data['category'] = $category_list;
                $this->data['orderno'] = $order_numb;
                $this->data['order'] = $order;
                $this->data['order_tracking'] = $_GET["pid"];
                $this->data['campaign_title'] = $campaign_title;
                $this->data['campaign_image'] = $campaign_image;
                $this->data['date_end'] = $date_end;
                $this->data['site'] = 'track';
                return view('shop::trackdetail', $this->data);
            } else {
                $error_lookup = 1;
            }
        }

        $searchcode = '';
        if (isset($_GET["pid"])) {
            $searchcode = $_GET["pid"];
        }

        $this->data['category'] = $category_list;
        $this->data['code'] = $searchcode;
        $this->data['orderno'] = $order_numb;
        $this->data['error_lookup'] = $error_lookup;
        $this->data['site'] = 'track';
        return view('shop::track', $this->data);
    }

    /**
     * Check promotion
     * @return Response
     */
    public function promotion()
    {
        $promotion = Http::get(config('config.pspApiPath') . '/promotions', ['code' => $_GET["code"], 'campaign' => $_GET["campaign"]]);
        return json_encode($promotion);
    }

    /**
     *
     */
    public function reportAnalytics()
    {
        $trackPrefix = config('config.analyticsPrefix');
        $analstr = isset($_COOKIE[$trackPrefix]) ? $_COOKIE[$trackPrefix] : '{}';
        $arrAnal = json_decode($analstr);
        $arrParam = [];
        if ($analstr) {
            $arrParam = ['url' => $arrAnal->url,
                'source' => $arrAnal->source
            ];
            if (isset($arrAnal->medium)) {
                $arrParam['medium'] = $arrAnal->medium;
            }
            if (isset($arrAnal->campaign)) {
                $arrParam['campaign'] = $arrAnal->campaign;
            }
            if (isset($arrAnal->content)) {
                $arrParam['content'] = $arrAnal->content;
            }
        }
        $analytic = Http::get(config('config.analyticsApiPath') . '/analytics', $arrParam);
        return json_encode($analytic);
    }
}
