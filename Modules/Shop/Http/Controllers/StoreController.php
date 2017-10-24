<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class StoreController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($url)
    {
        //Get category list
        $category_list = redis_category();
        //Get order number
        $order_numb = redis_order_number();

        //Get store data
        if (Redis::exists("/store/" . $url) && (!isset($_GET['c']) || (isset($_GET['c']) && $_GET['c'] != '1'))) {
            $page = Redis::get("/store/" . $url);
            $page = json_decode($page);
        } else {
            $store_data = Http::get(config('config.pspApiPath') . '/stores', ['url' => $url, 'campaign_details' => true]);
            if (!(array)$store_data) {
                $data = StoreController::error();
                return response()->view('errors.404', $data);
            }
            $page = array();
            $page['store'] = (object)array('title' => $store_data->title,
                'desc' => str_replace("%20", " ", urldecode($store_data->desc)),
                'banner' => $store_data->banner);
            $total = 0;
            if (isset($store_data->campaigns)) {
                foreach ($store_data->campaigns as $key => $item) {
                    foreach ($item->products as $key2 => $proditem) {
                        $variant = null;
                        foreach ($proditem->variants as $vitem) {
                            if ($vitem->default) {
                                $variant = $vitem;
                                break;
                            }
                        }
                        if (!$variant) {
                            $variant = $proditem->variants[0];
                        }

                        if ($variant && isset($variant->image)) {
                            $total++;
                            $res = [];
                            if ($proditem->back_view) {
                                $res['front'] = (isValidUrl($variant->image->back)) ? $variant->image->back : '';
                                $res['back'] = (isValidUrl($variant->image->front)) ? $variant->image->front : '';
                            } else {
                                $res['front'] = (isValidUrl($variant->image->front)) ? $variant->image->front : '';
                                $res['back'] = (isValidUrl($variant->image->back)) ? $variant->image->back : '';
                            }
                            //Get image _m
                            //if($res['front'] != '') $res['front'] = str_replace('.png','_m.png',$res['front']);
                            //if($res['back'] != '') $res['back'] = str_replace('.png','_m.png',$res['back']);

                            $page['data'][] = (object)array('id' => $proditem->id,
                                'price' => $proditem->price,
                                'image' => (object)$res,
                                'title' => $item->title,
                                'url' => $item->url,
                                'remaining' => $item->remaining);
                        }
                    }
                }
            }
            $page['total'] = $total;
            $page = (object)$page;
            Redis::set("/store/" . $url, json_encode($page));
            Redis::expire("/store/" . $url, 60 * 10);
        }

        //Get current page form url
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        //Define how many items we want to be visible in each page
        $perPage = 12;

        $page->data = array_splice($page->data, ($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        $result = new LengthAwarePaginator($page->data,
            $page->total,
            $perPage,
            LengthAwarePaginator::resolveCurrentPage(),
            ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return view('shop::store', ['pagejs' => genHtmlLoadFile(['js/shop/store.min.js']),
            'category' => $category_list,
            'orderno' => $order_numb,
            'pages' => $result,
            'store' => $page->store,
            'site' => 'store']);
    }

    /**
     * Show the form for error
     * @return Response
     */
    public function error()
    {
        //Get category list
        $category_list = redis_category();

        //Get order number
        $order_numb = redis_order_number();

        $more_data = StoreController::getRecentlyViewAndCampaignMore();

        return array('category' => $category_list,
            'orderno' => $order_numb,
            'site' => 'error',
            'recently_view' => $more_data['recently_view'],
            'more_campaign' => $more_data['more_campaign']);
    }

    private function getRecentlyViewAndCampaignMore()
    {
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
                        if (count($recently_view_data) == 4) break;

                    }
                }
            }
        }

        //More campaign
        if (Redis::exists("more-campaigns")) {
            $campaign_more = Redis::get("more-campaigns");
            $campaign_more = json_decode($campaign_more);
        } else {
            //Hard code category id
            $cat_more = '1V84sYfazfHFMilr';

            //Get campaign data
            $campaign_more = Http::get(config('config.pspApiPath') . '/campaigns', ['state' => 'launching', 'categories' => $cat_more, 'page' => 1, 'page_size' => 4]);
            if (isset($campaign_more->campaigns)) {
                $campaign_more = extendCampaignObj($campaign_more->campaigns);

                Redis::set("more-campaigns", json_encode($campaign_more));
                Redis::expire("more-campaigns", 60 * 10);
            } else {
                $campaign_more = false;
            }
        }
        return array('recently_view' => $recently_view_data,
            'more_campaign' => $campaign_more);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('shop::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('shop::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('shop::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
