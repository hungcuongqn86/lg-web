<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;

class ShopController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($url = '')
    {
        //Get category list
        $category_list = redis_category();
        //Get order number
        $order_numb = redis_order_number();

        //Check URL
        $subcategory_list = array();
        $parent_url = '';
        $slideindex = 0;

        if ($url == '' || Redis::exists("/" . $url)) { // It is category url, show campaign list
            //Get category selected and parent
            if (Redis::exists("/" . $url)) {
                $category_selected = json_decode(Redis::get("/" . $url));

                if (isset($category_selected->parent_id)) {
                    $categoryid = $category_selected->parent_id;
                    foreach (Redis::lrange('category-list', 0, -1) as $key) {
                        $category = json_decode(Redis::get($key));

                        if ($category->id == $categoryid) {
                            $category_selected->parent = $category;
                        }
                    }
                } else {
                    $categoryid = $category_selected->id;
                }

                //Get sub category list
                foreach (Redis::lrange('category-list', 0, -1) as $key) {
                    $category = json_decode(Redis::get($key));

                    if (isset($category->parent_id) && $category->parent_id == $categoryid) {
                        $subcategory_list[] = $category;
                    }
                }
                if (count($subcategory_list)) $subcategory_list = _array_sort($subcategory_list, "name");

                if (property_exists($category_selected, 'parent_id')) {
                    $parent_url = $category_selected->parent->url;
                    foreach ($subcategory_list as $index => $subcategory) {
                        if ($subcategory->id === $category_selected->id) {
                            $slideindex = $index;
                            break;
                        }
                    }
                }
            } else {
                $category_selected = (object)[
                    "id" => null,
                    "name" => "All",
                    "url" => "/"
                ];
            }

            //Get current page form url
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            //Define how many items we want to be visible in each page
            $perPage = 12;
            $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

            if (Redis::exists(url()->current() . "?page=" . $currentPage . '&sort=' . $sort)) {
                $campaign_data = Redis::get(url()->current() . "?page=" . $currentPage . '&sort=' . $sort);
                $campaign_data = (array)json_decode($campaign_data);
            } else {
                //Get campaign list
                $campaign_list = Http::get(config('config.pspApiPath') . '/campaigns', ['state' => 'launching', 'categories' => $category_selected->id, 'sort' => $sort, 'page' => $currentPage, 'page_size' => $perPage]);
                $campaign_data = array("total" => $campaign_list->total,
                    "data" => extendCampaignObj($campaign_list->campaigns));
                Redis::set(url()->current() . "?page=" . $currentPage . '&sort=' . $sort, json_encode($campaign_data));
                Redis::expire(url()->current() . "?page=" . $currentPage . '&sort=' . $sort, 60 * 10);
            }
            //Create our paginator and pass it to the view
            $result = new LengthAwarePaginator($campaign_data["data"],
                $campaign_data["total"],
                $perPage,
                LengthAwarePaginator::resolveCurrentPage(),
                ['path' => LengthAwarePaginator::resolveCurrentPath()]);

            return view('shop::index', ['pagejs' => genHtmlLoadFile(['js/shop/index.js']),
                'category' => $category_list,
                'orderno' => $order_numb,
                'subcategory' => $subcategory_list,
                'category_select' => $category_selected,
                'parent_url' => $parent_url,
                'slideindex' => $slideindex,
                'pages' => $result,
                'sort' => $sort,
                'site' => 'list']);
        } else {//It is campaign url, show campaign detail
            // dd($_SERVER);
            if (Redis::exists($url) && (!isset($_GET['c']) || (isset($_GET['c']) && $_GET['c'] != '1'))) {
                $campaign_selected = Redis::get($url);
                $campaign_selected = json_decode($campaign_selected);
                $campaign_selected->remaining = Redis::ttl($url) * 1000;
            } else {
                //Campaign detail
                $campaign_selected = Http::get(config('config.pspApiPath') . '/campaigns', ['url' => $url]);
                if ((array)$campaign_selected) {
                    $campaign_selected = extendCampaignObj(array($campaign_selected))[0];
                    $campaign_selected->desc = str_replace("%20", " ", urldecode($campaign_selected->desc));

                    //Format data
                    foreach ($campaign_selected->products as $key => $item) {
                        $campaign_selected->products[$key]->price = number_format($item->price, 2);
                        $variant_default_id = '';
                        $variant_default_name = '';
                        $image = '';
                        foreach ($item->variants as $variant) {
                            if ($variant->default == true) {
                                $variant_default_id = $variant->id;
                                $variant_default_name = $variant->name;
                                if ($item->back_view == false)
                                    $image = $variant->image->front;
                                else
                                    $image = $variant->image->back;
                                break;
                            }
                        }
                        if ($variant_default_id == '' && count($item->variants)) {
                            $variant_default_id = $item->variants[0]->id;
                            $variant_default_name = $item->variants[0]->name;
                            if ($item->back_view == false)
                                $image = $item->variants[0]->image->front;
                            else
                                $image = $item->variants[0]->image->back;
                        }
                        $campaign_selected->products[$key]->default_variant = $variant_default_id;
                        $campaign_selected->products[$key]->default_variant_name = $variant_default_name;
                        $campaign_selected->products[$key]->image = $image;
                    }
                    Redis::set($url, json_encode($campaign_selected));
                    Redis::expire($url, (int)($campaign_selected->remaining / 1000));
                } else {
                    $data = ShopController::error();
                    return response()->view('errors.404', $data);
                }
            }

            //Category
            if (isset($_GET['catid'])) {
                $cat_id = $_GET['catid'];
            } else {
                $cats = $campaign_selected->categories;
                $cat_id = explode(',', $campaign_selected->categories)[0];
            }

            $category_selected = [];
            foreach (Redis::lrange('category-list', 0, -1) as $key) {
                $category = json_decode(Redis::get($key));

                if ($category->id == $cat_id) {
                    $category_selected = $category;
                }
            }

            if (isset($category_selected->parent_id)) {
                foreach (Redis::lrange('category-list', 0, -1) as $key) {
                    $category = json_decode(Redis::get($key));

                    if ($category->id == $category_selected->parent_id) {
                        $category_selected->parent = $category;
                    }
                }
            }

            //Product
            if (isset($_GET['pid'])) {
                $p_id = $_GET['pid'];
            } else {
                foreach ($campaign_selected->products as $item) {
                    if ($item->default == true) {
                        $p_id = $item->id;
                        break;
                    }
                }
                if (!isset($p_id)) $p_id = $campaign_selected->products[0]->id;
            }

            //View
            if (isset($_GET['sid'])) {
                $s_id = (int)$_GET['sid'];
            } else {
                foreach ($campaign_selected->products as $item) {
                    if ($item->id == $p_id) $s_id = (int)$item->back_view;
                }
            }

            //Variant
            if (isset($_GET['cid'])) {
                $c_id = $_GET['cid'];
            } else {
                foreach ($campaign_selected->products as $item) {
                    if ($item->id == $p_id) {
                        foreach ($item->variants as $variant_item) {
                            if ($variant_item->default == true) {
                                $c_id = $variant_item->id;
                                break;
                            }
                        }
                        if (!isset($c_id)) $c_id = $item->variants[0]->id;
                    }
                }
            }

            //Recently viewed
            $recently_view_data = null;
            if (!isset($_COOKIE["recently_viewed"])) {
                $recently_view = array();
                $recently_view[] = (object)array("url" => $url, "time" => time());
                setcookie("recently_viewed", json_encode($recently_view), time() + 3600 * 24 * 10, "/");
            } else {
                //get recently view data
                $recently_view = json_decode($_COOKIE["recently_viewed"]);
                $check_add_view = 1;
                if ($recently_view && count($recently_view)) {
                    foreach ($recently_view as $key => $item) {
                        if (Redis::exists($item->url)) {
                            $view_data = Redis::get($item->url);
                            $view_data = json_decode($view_data);

                            if ($item->url == $url) {
                                $check_add_view = 0;
                                $recently_view[$key]->time = time();
                            } else $recently_view_data[] = $view_data;

                            if (count($recently_view_data) == 4) break;
                        }
                    }
                }

                //add recently view
                if ($check_add_view) {
                    $recently_view[] = (object)array("url" => $url, "time" => time());
                }

                $recently_view = _array_sort($recently_view, 'time', 1);
                if (count($recently_view) > 8) unset($recently_view[8]);
                setcookie("recently_viewed", json_encode($recently_view), time() + 3600 * 24 * 10, "/");
            }

            if (Redis::exists("more-campaigns")) {
                $campaign_more = Redis::get("more-campaigns");
                $campaign_more = json_decode($campaign_more);
            } else {
                //More campaign
                //Hard code category id
                $cat_more = '1V84sYfazfHFMilr';

                //Get campaign data
                $campaign_more = Http::get(config('config.pspApiPath') . '/campaigns', ['state' => 'launching', 'categories' => $cat_more, 'page' => 1, 'page_size' => 4]);
                $campaign_more = extendCampaignObj($campaign_more->campaigns);

                Redis::set("more-campaigns", json_encode($campaign_more));
                Redis::expire("more-campaigns", 60 * 10);
            }

            //Promotion code
            $pr = '';
            if (isset($_GET['pr'])) {
                $pr = $_GET['pr'];
                setcookie("pr_code", $pr, time() + 3600 * 24 * 10, "/");
            } else if (isset($_COOKIE["pr_code"])) {
                $pr = $_COOKIE["pr_code"];
            }

            return view('shop::show', ['pagejs' => genHtmlLoadFile(['js/shop/detail.min.js']),
                'category' => $category_list,
                'orderno' => $order_numb,
                'category_select' => $category_selected,
                'campaign_select' => $campaign_selected,
                'url' => $url,
                'pid' => $p_id,
                'cid' => $c_id,
                'sid' => $s_id,
                'pr_code' => $pr,
                'recently_view' => $recently_view_data,
                'more_campaign' => $campaign_more,
                'site' => 'detail']);
        }
    }

    private function getRecentlyViewAndCampaignMore($page_size = 4)
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
                        if (count($recently_view_data) == $page_size) break;

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
            $campaign_more = Http::get(config('config.pspApiPath') . '/campaigns', ['state' => 'launching', 'categories' => $cat_more, 'page' => 1, 'page_size' => $page_size]);
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
    public function payment()
    {
        //Get category list
        $category_list = redis_category();
        //In case post URL
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST["submit-cart"])) {
                $type = "cart";
            } elseif (isset($_POST["submit-checkout"])) {
                $type = "checkout";
            } elseif (Route::getFacadeRoot()->current()->uri() == 'shop/cart') {
                $type = "edit-cart";
            } elseif (isset($_POST["submit-order"]) || isset($_POST["stripeToken"])) {
                $type = "place-order";
            } else {
                $type = "edit-checkout";
            }


            //Old order
            if (isset($_COOKIE["orderid"])) {
                $old_order = Redis::get("order_" . $_COOKIE["orderid"]);
                $old_order = json_decode($old_order);
            }

            if ($type == "cart" || $type == "checkout") { //Create/update order
                //Post item
                $array_item = array();
                if (isset($_POST['order-style'])) {
                    for ($i = 0; $i < count($_POST['order-style']); $i++) {
                        $order_style = explode('#', $_POST['order-style'][$i]);
                        $pid = $order_style[0];
                        $cid = $order_style[1];
                        $ctitle = $order_style[2];
                        $sizeid = $_POST['order-size'][$i];
                        $quantity = $_POST['order-quantity'][$i];
                        $array_item[] = array('campaign_id' => $_POST['campaignid'],
                            'product_id' => $pid,
                            'variant_id' => $cid,
                            'variant_name' => $ctitle,
                            'size_id' => $sizeid,
                            'quantity' => $quantity);
                    }
                }

                //promotion
                $promotion_code = '';
                if (isset($_COOKIE["pr_code"])) {
                    $promotion_code = $_COOKIE["pr_code"];
                }
                $promotion = array('code' => $promotion_code);

                if (isset($old_order)) {
                    //Update order
                    foreach ($old_order->items as $item) {
                        $array_item[] = array('id' => $item->id,
                            'quantity' => $item->quantity);
                    }

                    $order = Http::put(config('config.pspApiPath') . '/orders/' . $old_order->id, ['items' => $array_item, 'promotion' => $promotion]);
                } else {
                    //Create order
                    $arr_header = null;
                    if (isset($_COOKIE['shipping_id']) && $_COOKIE['shipping_id'] != '') {
                        $arr_header = array('X-Shipping-Id: ' . $_COOKIE['shipping_id']);
                    }
                    $order = Http::post(config('config.pspApiPath') . '/orders', ['currency' => 'USD', 'items' => $array_item, 'promotion' => $promotion], false, $arr_header);

                    setcookie("orderid", $order->id, time() + 3600 * 24 * 365, "/");
                }
            } elseif ($type == "edit-cart") { //Edit order
                $array_item = array();
                if (isset($_POST['order_item_id'])) {

                    for ($i = 0; $i < count($_POST['order_item_id']); $i++) {
                        $array_item[] = array('id' => $_POST['order_item_id'][$i],
                            'quantity' => $_POST['order_item_quantity'][$i]);
                    }
                }
                $order = Http::put(config('config.pspApiPath') . '/orders/' . $_COOKIE["orderid"], ['items' => $array_item]);

                if (!isset($_POST['order_item_id'])) {
                    //remove redis order
                    Redis::del("order_" . $_COOKIE["orderid"]);
                    //remove cookie order id
                    setcookie("orderid", false, time() - 3600, "/");
                }
            } elseif ($type == "edit-checkout") { //Edit checkout
                $array_item = array();
                foreach ($old_order->items as $item) {
                    $array_item[] = array('id' => $item->id,
                        'quantity' => $item->quantity);
                }
                $address = array('line1' => $_POST['order-address-line1'],
                    'line2' => $_POST['order-address-line2'],
                    'city' => $_POST['order-city'],
                    'state' => '',
                    'postal_code' => $_POST['order-postal-code'],
                    'country' => $_POST['order-country']);
                $shipping = array('name' => $_POST['order-name'],
                    'email' => $_POST['order-email'],
                    'phone' => $_POST['order-phone'],
                    'address' => $address);

                $order = Http::put(config('config.pspApiPath') . '/orders/' . $_COOKIE["orderid"], ['items' => $array_item, 'shipping' => $shipping]);
            } else { //Place order

                //Update order
                $array_item = array();
                foreach ($old_order->items as $item) {
                    $array_item[] = array('id' => $item->id,
                        'quantity' => $item->quantity);
                }
                $address = array('line1' => $_POST['order-address-line1'],
                    'line2' => $_POST['order-address-line2'],
                    'city' => $_POST['order-city'],
                    'state' => $_POST['order-state'],
                    'postal_code' => $_POST['order-postal-code'],
                    'country' => $_POST['order-country']);
                $shipping = array('name' => $_POST['order-name'],
                    'email' => $_POST['order-email'],
                    'phone' => $_POST['order-phone'],
                    'address' => $address);

                $order = Http::put(config('config.pspApiPath') . '/orders/' . $_COOKIE["orderid"], ['items' => $array_item, 'shipping' => $shipping]);

                //Save shipping id
                if (isset($_POST['save-address-info'])) {
                    setcookie("shipping_id", $order->shipping->id, time() + 3600 * 24 * 30, "/");
                }
                $shipping['id'] = $order->shipping->id;
                $shipping['gift'] = isset($_POST['ship-as-gift']) ? true : false;

                $subscribe = isset($_POST['check_subscribe']) ? 1 : 0;

                if ($_POST['payment-method'] == 1) {
                    //Create payment with paypal
                    $payment = Http::post(config('config.pspApiPath') . '/orders/' . $_COOKIE["orderid"] . '/payments', ['method' => 'paypal', 'shipping' => $shipping, 'subscribe' => $subscribe]);
                } else {
                    //Create payment with card
                    $payment = Http::post(config('config.pspApiPath') . '/orders/' . $_COOKIE["orderid"] . '/payments', ['method' => $_POST["payment_card_type"], 'shipping' => $shipping, 'subscribe' => $subscribe, 'token' => $_POST["stripeToken"]]);
                }
            }

            //Get total
            $total_quantity = 0;
            $total_price = 0;
            $total_shipping = 0;
            $total_discount = 0;
            foreach ($order->items as $key => $item) {
                $total_quantity += $item->quantity;
                $total_price += $item->quantity * $item->price;
                $total_shipping += $item->shipping_fee;
                $order->items[$key]->price = number_format($item->price, 2);
                $order->items[$key]->shipping_fee = number_format($item->shipping_fee, 2);
                if (isset($item->promotion)) {
                    $total_discount += $item->promotion->discount->amount;
                }
            }
            $order->total_quantity = $total_quantity;
            $order->total_price = number_format($total_price, 2);
            $order->total_shipping = number_format($total_shipping, 2);
            if ($total_discount) {
                $order->total_discount = number_format($total_discount, 2);
            }
            $order->amount = number_format($order->amount, 2);

            Redis::set("order_" . $order->id, json_encode($order));
            Redis::expire("order_" . $order->id, 60 * 60 * 24 * 7);

            $order_numb = $total_quantity;

            if ($type == "place-order") {
                if ($_POST['payment-method'] == 1) { //Paypal
                    $redirect_link = '';
                    if ($payment && isset($payment->information) && isset($payment->information->links)) {
                        $links = $payment->information->links;
                        if ($links && count($links)) {
                            foreach ($links as $link) {
                                if ($link->method == 'REDIRECT') {
                                    $redirect_link = $link->href;
                                }
                            }
                        }
                        if ($redirect_link != '') return redirect($redirect_link);
                    }
                } else { //Cart
                    if (isset($payment->state) && $payment->state == 'approved') { //Finish payment
                        $order = Http::get(config('config.pspApiPath') . '/orders/' . $_COOKIE["orderid"]);
                        //Get total
                        $total_quantity = 0;
                        $total_price = 0;
                        $total_shipping = 0;
                        $total_discount = 0;
                        foreach ($order->items as $key => $item) {
                            $total_quantity += $item->quantity;
                            $total_price += $item->quantity * $item->price;
                            $total_shipping += $item->shipping_fee;
                            $order->items[$key]->price = number_format($item->price, 2);
                            $order->items[$key]->shipping_fee = number_format($item->shipping_fee, 2);
                            if (isset($item->promotion)) {
                                $total_discount += $item->promotion->discount->amount;
                            }
                        }
                        $order->total_quantity = $total_quantity;
                        $order->total_price = number_format($total_price, 2);
                        $order->total_shipping = number_format($total_shipping, 2);
                        if ($total_discount) {
                            $order->total_discount = number_format($total_discount, 2);
                        }
                        $order->amount = number_format($order->amount, 2);

                        Redis::set("order_" . $order->id, json_encode($order));
                        return redirect('/shop/thankyou');
                    }
                }
            }
        } else { //In case get URL
            if (Route::getFacadeRoot()->current()->uri() == 'shop/cart') {
                $type = "cart";
            } else {
                $type = "checkout";
            }

            //Order
            if (isset($_COOKIE["orderid"])) {
                $order = Redis::get("order_" . $_COOKIE["orderid"]);
                $order = json_decode($order);
            } else {
                $order = null;
            }

            //Get order number
            $order_numb = redis_order_number();

            //Check existing $_GET paymentid, payerid,token to excute payment
            if (!empty($_GET['paymentId']) && !empty($_GET['token']) && !empty($_GET['PayerID'])) {
                $payment = Http::post(config('config.pspApiPath') . '/payments/execute', ['txn_id' => $_GET['paymentId'], 'token_id' => $_GET['token'], 'payer_id' => $_GET['PayerID']]);
                if (isset($payment->state) && $payment->state == 'approved') { //Finish payment
                    $order = Http::get(config('config.pspApiPath') . '/orders/' . $_COOKIE["orderid"]);
                    //Get total
                    $total_quantity = 0;
                    $total_price = 0;
                    $total_shipping = 0;
                    $total_discount = 0;
                    foreach ($order->items as $key => $item) {
                        $total_quantity += $item->quantity;
                        $total_price += $item->quantity * $item->price;
                        $total_shipping += $item->shipping_fee;
                        $order->items[$key]->price = number_format($item->price, 2);
                        $order->items[$key]->shipping_fee = number_format($item->shipping_fee, 2);
                        if (isset($item->promotion)) {
                            $total_discount += $item->promotion->discount->amount;
                        }
                    }
                    $order->total_quantity = $total_quantity;
                    $order->total_price = number_format($total_price, 2);
                    $order->total_shipping = number_format($total_shipping, 2);
                    if ($total_discount) {
                        $order->total_discount = number_format($total_discount, 2);
                    }
                    $order->amount = number_format($order->amount, 2);

                    Redis::set("order_" . $order->id, json_encode($order));
                    return redirect('/shop/thankyou');
                }
            }
        }

        if ($type == "cart" || $type == "edit-cart") {
            $more_data = ShopController::getRecentlyViewAndCampaignMore(8);
            return view('shop::cart', [
                'pagejs' => genHtmlLoadFile(['js/shop/cart.min.js']),
                'category' => $category_list,
                'order' => $order,
                'orderno' => $order_numb,
                'recently_view' => $more_data['recently_view'],
                'more_campaign' => $more_data['more_campaign'],
                'site' => 'cart']);

        } else {
            if (isset($_POST["submit-checkout"])) {
                return redirect('/shop/checkout');
            } elseif ($order_numb == null) {
                return redirect('/shop/cart');
            } else {
                $country_data = get_country_data();
                $state_data = get_state_data();
                $state_combo = isset($state_data[$order->shipping->address->country]) ? $state_data[$order->shipping->address->country] : null;

                //Get campaign in order
                $arr_campaign = array();
                foreach ($order->items as $item) {
                    if (!in_array($item->campaign_url, $arr_campaign)) $arr_campaign[] = $item->campaign_url;
                }

                $arr_campaign_data = array();
                foreach ($arr_campaign as $campaign_url) {
                    $campaign_selected = Http::get(config('config.pspApiPath') . '/campaigns', ['url' => substr($campaign_url, 1)]);
                    if (isset($campaign_selected->fb_pixel) && $campaign_selected->fb_pixel != '') {
                        $quantity = 0;
                        $price = 0;
                        foreach ($order->items as $item) {
                            if ($item->campaign_url == $campaign_url) {
                                $quantity += $item->quantity;
                                $price += $item->quantity * $item->price;
                            }
                        }
                        $price = round($price, 2);
                        $arr_campaign_data[] = (object)array('url' => $campaign_url,
                            'fb_pixel' => $campaign_selected->fb_pixel,
                            'quantity' => $quantity,
                            'price' => $price);
                    }
                }

                //Check error place order
                $validation_error = array();
                if ($type == "place-order") {
                    if ($payment && isset($payment->state) && $payment->state == 'fail' && isset($payment->reason) && $payment->reason->name == 'VALIDATION_ERROR') {
                        foreach ($payment->reason->details as $item) {
                            $validation_error[] = $item->field;
                        }
                    } else {
                        $validation_error[] = 'connection';
                    }
                }

                //Define payment card type (stripe || brain tree)
                $payment_card_type = 'stripe';
                //$payment_card_type = 'braintree';

                $brain_tree_key = '';
                if ($payment_card_type == 'braintree') {
                    $brain_tree = Http::get(config('config.domain') . '/payments/token/braintree');
                    if (isset($brain_tree->token)) $brain_tree_key = $brain_tree->token;
                }

                return view('shop::checkout', ['pagejs' => genHtmlLoadFile(['js/shop/checkout.min.js']),
                    'category' => $category_list,
                    'order' => $order,
                    'orderno' => $order_numb,
                    'country' => $country_data,
                    'state' => $state_combo,
                    'validation_error' => $validation_error,
                    'campaign_pixel' => $arr_campaign_data,
                    'payment_card_type' => $payment_card_type,
                    'brain_tree_key' => $brain_tree_key,
                    'site' => 'checkout']);
            }
        }
    }

    /**
     * Show the form for finish
     * @return Response
     */
    public function thankyou()
    {
        //Get category list
        $category_list = redis_category();

        if (isset($_COOKIE["orderid"])) {
            $order = Redis::get("order_" . $_COOKIE["orderid"]);
            $order = json_decode($order);
        }

        if (isset($order) && $order->state == 'placed') {
            //if (isset($order)) {
            //remove redis order
            Redis::del("order_" . $_COOKIE["orderid"]);
            //remove cookie order id
            setcookie("orderid", false, time() - 3600, "/");

            $chk_same_campaign = 1;
            $campaign_url = $order->items[0]->campaign_url;
            foreach ($order->items as $item) {
                if ($item->campaign_url != $campaign_url) {
                    $chk_same_campaign = 0;
                    break;
                }
            }

            $date_cur = '';
            $date_end = '';
            $date_order = '';
            if ($chk_same_campaign) {
                //Get end campaign
                $campaign_selected = Http::get(config('config.pspApiPath') . '/campaigns', ['url' => substr($campaign_url, 1)]);
                $date_cur = date("M jS");
                $date_end = date("M jS", strtotime($campaign_selected->end_time));
                $date_order = date("M jS", strtotime("+7 day", strtotime($campaign_selected->end_time)));
            }

            //Get campaign in order
            $arr_campaign = array();
            foreach ($order->items as $item) {
                if (!in_array($item->campaign_url, $arr_campaign)) $arr_campaign[] = $item->campaign_url;
            }

            $arr_campaign_data = array();
            foreach ($arr_campaign as $campaign_url) {
                $campaign_selected = Http::get(config('config.pspApiPath') . '/campaigns', ['url' => substr($campaign_url, 1)]);
                if (isset($campaign_selected->fb_pixel) && $campaign_selected->fb_pixel != '') {
                    $quantity = 0;
                    $price = 0;
                    foreach ($order->items as $item) {
                        if ($item->campaign_url == $campaign_url) {
                            $quantity += $item->quantity;
                            $price += $item->quantity * $item->price;
                        }
                    }
                    $price = round($price, 2);
                    $arr_campaign_data[] = (object)array('url' => $campaign_url,
                        'fb_pixel' => $campaign_selected->fb_pixel,
                        'quantity' => $quantity,
                        'price' => $price);
                }
            }

            return view('shop::thankyou', ['category' => $category_list,
                'orderno' => null,
                'order' => $order,
                'date_cur' => $date_cur,
                'date_end' => $date_end,
                'date_order' => $date_order,
                'campaign_pixel' => $arr_campaign_data,
                'site' => 'thankyou']);
        } else {
            return redirect('/');
        }

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function search()
    {
        $search = '';
        if (isset($_GET['term'])) $search = trim($_GET['term']);
        if ($search == '') {
            return redirect('/shop');
        }
        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
        //Get category list
        $category_list = redis_category();
        //Get order number
        $order_numb = redis_order_number();

        //Get current page form url
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        //Define how many items we want to be visible in each page
        $perPage = 12;

        //Get campaign list
        $campaign_list = Http::get(config('config.pspApiPath') . '/campaigns', ['state' => 'launching', 'title' => $search, 'page' => $currentPage, 'page_size' => $perPage, 'sort' => $sort]);

        //Create our paginator and pass it to the view
        $result = new LengthAwarePaginator(extendCampaignObj($campaign_list->campaigns),
            $campaign_list->total,
            $perPage,
            LengthAwarePaginator::resolveCurrentPage(),
            ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        $more_data = ShopController::getRecentlyViewAndCampaignMore(8);

        return view('shop::search', ['pagejs' => genHtmlLoadFile(['js/shop/search.js']),
            'category' => $category_list,
            'orderno' => $order_numb,
            'search' => $search,
            'total' => $campaign_list->total,
            'pages' => $result,
            'recently_view' => $more_data['recently_view'],
            'more_campaign' => $more_data['more_campaign'],
            'sort' => $sort,
            'site' => 'search']);
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

        $more_data = ShopController::getRecentlyViewAndCampaignMore();

        return array('category' => $category_list,
            'orderno' => $order_numb,
            'site' => 'error',
            'recently_view' => $more_data['recently_view'],
            'more_campaign' => $more_data['more_campaign']);
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
