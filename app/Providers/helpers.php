<?php

function get_ip_address()
{
    $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                // trim for safety measures
                $ip = trim($ip);
                // attempt to validate IP
                if (validate_ip($ip)) {
                    return $ip;
                }
            }
        }
    }
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
}

/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 */
function validate_ip($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    return true;
}

function _array_sort($array, $key, $sort_order = 0, $type = 0)
{
    for ($i = 0; $i < sizeof($array); $i++) {
        $sort_values[$i] = ($array[$i]->$key);
    }

    if ($sort_order == 0) {
        asort($sort_values);
    } else {
        arsort($sort_values);
    }


    reset($sort_values);
    while (list($arr_key, $arr_val) = each($sort_values)) {
        $sorted_arr[] = $array[$arr_key];
    }
    return $sorted_arr;
}

function redis_category()
{
    //Get category list
    $category_list = array();
    //Use redis to create memo category data
    if (Redis::exists('category-list')) {
        foreach (Redis::lrange('category-list', 0, -1) as $key) {
            if (Redis::exists($key)) {
                $category = json_decode(Redis::get($key));
                if (!isset($category->parent_id)) {
                    $category_list[] = $category;
                }
            }
        }
    }
    if (!count($category_list)) {
        Redis::del('category-list');
        $category_response = Http::get(config('config.pspApiPath') . '/categories');
        foreach ($category_response->categories as $item) {
            Redis::set($item->url, json_encode($item));
            Redis::expire($item->url, 60 * 60 * 24);
            Redis::lpush("category-list", $item->url);

            $category_list[] = $item;
            $subcategory_response = Http::get(config('config.pspApiPath') . '/categories', ['parent_id' => $item->id]);
            foreach ($subcategory_response->categories as $subitem) {
                Redis::set($subitem->url, json_encode($subitem));
                Redis::expire($subitem->url, 60 * 60 * 24);
                Redis::lpush("category-list", $subitem->url);
            }
        }
        Redis::expire("category-list", 60 * 60 * 24);
    }
    if (count($category_list)) {
        $category_list = _array_sort($category_list, "name");
    }
    return $category_list;
}

function redis_order_number()
{
    $order_number = null;
    if (isset($_COOKIE["orderid"]) && Redis::exists("order_" . $_COOKIE["orderid"])) {
        $order = Redis::get("order_" . $_COOKIE["orderid"]);
        $order = json_decode($order);
        $order_number = $order->total_quantity;
    }

    return $order_number;
}

function extendCampaignObj($campObj)
{
    foreach ($campObj as $key => $item) {
        $campObj[$key]->price = '0';
        $campObj[$key]->image = '';
        foreach ($item->products as $proditem) {
            if ($proditem->default == true) {
                $campObj[$key]->price = number_format($proditem->price, 2);
                foreach ($proditem->variants as $variant_item) {
                    if ($variant_item->default == true) {
                        if ($proditem->back_view == false) {
                            $campObj[$key]->image = $variant_item->image->front;
                        } else {
                            $campObj[$key]->image = $variant_item->image->back;
                        }
                        break;
                    }
                }
                if ($campObj[$key]->image == '' && isset($proditem->variants[0])) {
                    $campObj[$key]->image = $proditem->variants[0]->image->front;
                }
                break;
            }
        }
        if ($campObj[$key]->price == '0' || $campObj[$key]->image == '') array_splice($campObj, $key, 1);
    }
    return $campObj;
}

function get_country_data()
{
    $array_country = array(["id" => "BKrv6cuhQfIKZt5D", "name" => "Afghanistan", "code" => "AF"],
        ["id" => "88k1Oj520bZCLIMi", "name" => "Åland Islands", "code" => "AX"],
        ["id" => "FGHHN-x1pR4j-VOb", "name" => "Albania", "code" => "AL"],
        ["id" => "uFc_cdEpmDVOnGVc", "name" => "Algeria", "code" => "DZ"],
        ["id" => "SxWarf-W7RjqzrIp", "name" => "American Samoa", "code" => "AS"],
        ["id" => "mFzw8MwXuS37C5lv", "name" => "Andorra", "code" => "AD"],
        ["id" => "g333qSEMMGebjQjx", "name" => "Angola", "code" => "AO"],
        ["id" => "EvCNICRJ3I5v0ESY", "name" => "Anguilla", "code" => "AI"],
        ["id" => "dsZlyahlsxGYVrNq", "name" => "Antarctica", "code" => "AQ"],
        ["id" => "kPafIPTyYS6F9ysV", "name" => "Antigua And Barbuda", "code" => "AG"],
        ["id" => "YUZbwyrs5mvpU8Br", "name" => "Argentina", "code" => "AR"],
        ["id" => "FzetJCNNxnJtHH1U", "name" => "Armenia", "code" => "AM"],
        ["id" => "GLlKzV2DHTMJtyyG", "name" => "Aruba", "code" => "AW"],
        ["id" => "vr-_9KbU8KSY3IA2", "name" => "Australia", "code" => "AU"],
        ["id" => "6tmuPYy8PWAEZfCu", "name" => "Austria", "code" => "AT"],
        ["id" => "z66ukhQmiQtInXbG", "name" => "Azerbaijan", "code" => "AZ"],
        ["id" => "pxvgfj7Q7gU55guK", "name" => "Bahamas", "code" => "BS"],
        ["id" => "RJg-ifep0uPeWyFl", "name" => "Bahrain", "code" => "BH"],
        ["id" => "SEGq6SC6NBQw4tP9", "name" => "Bangladesh", "code" => "BD"],
        ["id" => "MIO1hTLWkgZH-pwt", "name" => "Barbados", "code" => "BB"],
        ["id" => "599NHP0d6JJ3yzSS", "name" => "Belarus", "code" => "BY"],
        ["id" => "hfraeBTPpwVppAB-", "name" => "Belgium", "code" => "BE"],
        ["id" => "9BMPq9jKk1lQSl0Y", "name" => "Belize", "code" => "BZ"],
        ["id" => "FmQ_x0UNPppQB42C", "name" => "Benin", "code" => "BJ"],
        ["id" => "P0-DZNphFa3cdRd2", "name" => "Bermuda", "code" => "BM"],
        ["id" => "lSjHIy1N7eIqXiSk", "name" => "Bhutan", "code" => "BT"],
        ["id" => "lX4TBdNbv9_gm27h", "name" => "Bolivia, Plurinational State Of", "code" => "BO"],
        ["id" => "Ni747VbCtQS0B7m-", "name" => "Bonaire, Sint Eustatius And Saba", "code" => "BQ"],
        ["id" => "jaN66At1gdFSTaZe", "name" => "Bosnia And Herzegovina", "code" => "BA"],
        ["id" => "xyywj-QKmm8IjYTg", "name" => "Botswana", "code" => "BW"],
        ["id" => "zZ5eMLbupv8wCLV8", "name" => "Bouvet Island", "code" => "BV"],
        ["id" => "05B2msDp0-wVBoFr", "name" => "Brazil", "code" => "BR"],
        ["id" => "uStoaoTOobeorjKD", "name" => "British Indian Ocean Territory", "code" => "IO"],
        ["id" => "p8reQL8mJM8x9y2-", "name" => "Brunei Darussalam", "code" => "BN"],
        ["id" => "8wbDRUB3yH5riHKL", "name" => "Bulgaria", "code" => "BG"],
        ["id" => "Et7KHbdu0IYu3951", "name" => "Burkina Faso", "code" => "BF"],
        ["id" => "UcJ_Wmco3Gdl_vNP", "name" => "Burundi", "code" => "BI"],
        ["id" => "CqmtHyZ5q5HasJQV", "name" => "Cambodia", "code" => "KH"],
        ["id" => "xwJMQkplCwsvK5Ee", "name" => "Cameroon", "code" => "CM"],
        ["id" => "DZe3L-Gxv0bQW7Ac", "name" => "Canada", "code" => "CA"],
        ["id" => "s78N9ib-plAt7JyP", "name" => "Cape Verde", "code" => "CV"],
        ["id" => "soWhYGwGaWHSNqEW", "name" => "Cayman Islands", "code" => "KY"],
        ["id" => "eEuaGvI9dL0Ex614", "name" => "Central African Republic", "code" => "CF"],
        ["id" => "k9z_6A77VCN21QAH", "name" => "Chad", "code" => "TD"],
        ["id" => "1anzXDMfXg9HsQuX", "name" => "Chile", "code" => "CL"],
        ["id" => "7mWIN7e1L1kZpyfr", "name" => "China", "code" => "CN"],
        ["id" => "mDLcQcd1zk7Q2BqT", "name" => "Christmas Island", "code" => "CX"],
        ["id" => "HXeK5TwysB7aQSmY", "name" => "Cocos (keeling) Islands", "code" => "CC"],
        ["id" => "dxT82pq6ON4VOBKg", "name" => "Colombia", "code" => "CO"],
        ["id" => "pE6QIdr34yW3Es0s", "name" => "Comoros", "code" => "KM"],
        ["id" => "r3do76Qlo0CVInUB", "name" => "Congo", "code" => "CG"],
        ["id" => "SmAqp1KLRDEgF6UK", "name" => "Congo, The Democratic Republic Of The", "code" => "CD"],
        ["id" => "nv9NdfVFNU-ndJbu", "name" => "Cook Islands", "code" => "CK"],
        ["id" => "UUe0sc75AJZ3sR0u", "name" => "Costa Rica", "code" => "CR"],
        ["id" => "3pz1lyavZCvjgFKl", "name" => "Côte D ivoire", "code" => "CI"],
        ["id" => "9CaZ4MCHbEe16NKE", "name" => "Croatia", "code" => "HR"],
        ["id" => "6y1zev-oU5S7MhLJ", "name" => "Cuba", "code" => "CU"],
        ["id" => "xEjfW3Q_Tu3tXclZ", "name" => "Curaçao", "code" => "CW"],
        ["id" => "QXuymljeUtUZ6j4N", "name" => "Cyprus", "code" => "CY"],
        ["id" => "TYvaCUK8MM6_hvb2", "name" => "Czech Republic", "code" => "CZ"],
        ["id" => "CTTwDBHEAbUZ6iWp", "name" => "Denmark", "code" => "DK"],
        ["id" => "AR4owPXk_1j024qe", "name" => "Djibouti", "code" => "DJ"],
        ["id" => "YABSJf6DMAwJuSa3", "name" => "Dominica", "code" => "DM"],
        ["id" => "5K__lkWMjSdy6vK6", "name" => "Dominican Republic", "code" => "DO"],
        ["id" => "XtxARhuMhcY__BbQ", "name" => "Ecuador", "code" => "EC"],
        ["id" => "uXJdB1GK9899-s7f", "name" => "Egypt", "code" => "EG"],
        ["id" => "tKlND7SYmLp4XJYS", "name" => "El Salvador", "code" => "SV"],
        ["id" => "kP6h8URYGbI6FsBc", "name" => "Equatorial Guinea", "code" => "GQ"],
        ["id" => "7r8jvQfDN6kkHZBk", "name" => "Eritrea", "code" => "ER"],
        ["id" => "fJxg_iYlNWHJL8F4", "name" => "Estonia", "code" => "EE"],
        ["id" => "7wNCEGSEnONdlMjB", "name" => "Ethiopia", "code" => "ET"],
        ["id" => "YED3nXzbRFTjvnh2", "name" => "Falkland Islands (malvinas)", "code" => "FK"],
        ["id" => "wFK09DGGPVFKk1RO", "name" => "Faroe Islands", "code" => "FO"],
        ["id" => "XcIbP_xjcHIvaRPL", "name" => "Fiji", "code" => "FJ"],
        ["id" => "4nd6hdy_yisWnguB", "name" => "Finland", "code" => "FI"],
        ["id" => "Ls4THWluL1nTdVAK", "name" => "France", "code" => "FR"],
        ["id" => "UviZX-IjetEMWMHE", "name" => "French Guiana", "code" => "GF"],
        ["id" => "uXhL1TQeh_fGiRpY", "name" => "French Polynesia", "code" => "PF"],
        ["id" => "E_uuqbmaJDi2b0lv", "name" => "French Southern Territories", "code" => "TF"],
        ["id" => "WTwlR-F_4UopElx6", "name" => "Gabon", "code" => "GA"],
        ["id" => "C5LmGkqlym4hScUE", "name" => "Gambia", "code" => "GM"],
        ["id" => "O36toO6tPKz-2wp4", "name" => "Georgia", "code" => "GE"],
        ["id" => "9mCefy2_zNExNODJ", "name" => "Germany", "code" => "DE"],
        ["id" => "xJL3RKRYo9kY5XVB", "name" => "Ghana", "code" => "GH"],
        ["id" => "t7Da1B1BP2n86IKn", "name" => "Gibraltar", "code" => "GI"],
        ["id" => "53KRrlVJ7NzepSL6", "name" => "Greece", "code" => "GR"],
        ["id" => "98IZ_Z59QVKHXElR", "name" => "Greenland", "code" => "GL"],
        ["id" => "JTNkgV_S-X356Fw1", "name" => "Grenada", "code" => "GD"],
        ["id" => "W_QybZzATmo_Krvh", "name" => "Guadeloupe", "code" => "GP"],
        ["id" => "N9HQP3eoWXm6Gx91", "name" => "Guam", "code" => "GU"],
        ["id" => "_FPgCT6rb0w7twQB", "name" => "Guatemala", "code" => "GT"],
        ["id" => "H2r_XTq_9BAKi96s", "name" => "Guernsey", "code" => "GG"],
        ["id" => "CpD2nfGVJ4u0c4Qw", "name" => "Guinea", "code" => "GN"],
        ["id" => "n7UUi-E2yXBOBQ_V", "name" => "Guinea-bissau", "code" => "GW"],
        ["id" => "KpiczJo5kgaeH0ty", "name" => "Guyana", "code" => "GY"],
        ["id" => "XAAlwgrIQFC_G7GQ", "name" => "Haiti", "code" => "HT"],
        ["id" => "erSGpSryGXmPIFBN", "name" => "Heard Island And Mcdonald Islands", "code" => "HM"],
        ["id" => "T5lC_nDeODdL8Srp", "name" => "Holy See (vatican City State)", "code" => "VA"],
        ["id" => "8Odf0CtMdfGjB1vq", "name" => "Honduras", "code" => "HN"],
        ["id" => "qPaONWNN4esr2duY", "name" => "Hong Kong", "code" => "HK"],
        ["id" => "DJq0_lwiZaGfSKBo", "name" => "Hungary", "code" => "HU"],
        ["id" => "6BA8KwKFvBnBn6si", "name" => "Iceland", "code" => "IS"],
        ["id" => "c3l_NLoXsuiVawWv", "name" => "India", "code" => "IN"],
        ["id" => "B1UdJ0vrO3ZbXRrG", "name" => "Indonesia", "code" => "ID"],
        ["id" => "nE65ekIuOOSodk-0", "name" => "Iran, Islamic Republic Of", "code" => "IR"],
        ["id" => "xUmyloeLi2OxySJ4", "name" => "Iraq", "code" => "IQ"],
        ["id" => "pa5EEoiyoba4K_le", "name" => "Ireland", "code" => "IE"],
        ["id" => "pMytIeRPwi2ZEHv7", "name" => "Isle Of Man", "code" => "IM"],
        ["id" => "L21bn4pI2hn2hGwg", "name" => "Israel", "code" => "IL"],
        ["id" => "okr6_HB7tA1gZVjI", "name" => "Italy", "code" => "IT"],
        ["id" => "2p3WHisnMr-kENq7", "name" => "Jamaica", "code" => "JM"],
        ["id" => "heacFjBdysJ36OTt", "name" => "Japan", "code" => "JP"],
        ["id" => "FUtzpoluHCI2R4DG", "name" => "Jersey", "code" => "JE"],
        ["id" => "L4ey7lpuFfSYZBGF", "name" => "Jordan", "code" => "JO"],
        ["id" => "8zGhdddW1u_pEJWI", "name" => "Kazakhstan", "code" => "KZ"],
        ["id" => "pZneJlXgo9q9xZej", "name" => "Kenya", "code" => "KE"],
        ["id" => "EnoI03-V44Dke1EE", "name" => "Kiribati", "code" => "KI"],
        ["id" => "enmPI_pRb9Uwh6dU", "name" => "Korea, Democratic People s Republic Of", "code" => "KP"],
        ["id" => "Ewc9oJJ6cA0Tlpe_", "name" => "Korea, Republic Of", "code" => "KR"],
        ["id" => "ZOOy8ULpICiyeIdz", "name" => "Kuwait", "code" => "KW"],
        ["id" => "x-_eRbmB9cscR_4-", "name" => "Kyrgyzstan", "code" => "KG"],
        ["id" => "Tk9Cmy9lZMsjw3Bc", "name" => "Lao People Democratic Republic", "code" => "LA"],
        ["id" => "hH1TgAFefbTf7lfx", "name" => "Latvia", "code" => "LV"],
        ["id" => "jYyP26cbjeI5QHpN", "name" => "Lebanon", "code" => "LB"],
        ["id" => "yHOgKDG7PS8WprVT", "name" => "Lesotho", "code" => "LS"],
        ["id" => "1MU0y-lrQMYiVEP8", "name" => "Liberia", "code" => "LR"],
        ["id" => "ShOr_YXH6zqBFkkc", "name" => "Libya", "code" => "LY"],
        ["id" => "GHUFERyXkleI-low", "name" => "Liechtenstein", "code" => "LI"],
        ["id" => "1FsjtObsznRevMKE", "name" => "Lithuania", "code" => "LT"],
        ["id" => "X3waNrFQJQj9YznH", "name" => "Luxembourg", "code" => "LU"],
        ["id" => "fxuJXz07HCNAeMAi", "name" => "Macao", "code" => "MO"],
        ["id" => "mBpGaqQP_n5p_B3G", "name" => "Macedonia, The Former Yugoslav Republic Of", "code" => "MK"],
        ["id" => "alr-FsHytyvAJxud", "name" => "Madagascar", "code" => "MG"],
        ["id" => "ml4HYyR-IiqLO6Dm", "name" => "Malawi", "code" => "MW"],
        ["id" => "ycr5X7bDiH8Dhn7Z", "name" => "Malaysia", "code" => "MY"],
        ["id" => "qmFcx0JZNPg0DoWp", "name" => "Maldives", "code" => "MV"],
        ["id" => "npcGVO1mV5v4ChO7", "name" => "Mali", "code" => "ML"],
        ["id" => "lfCnuw6FEIAJKeVY", "name" => "Malta", "code" => "MT"],
        ["id" => "rpRGp-R4t2FjtwqA", "name" => "Marshall Islands", "code" => "MH"],
        ["id" => "7uhclLcOdVfXx6ph", "name" => "Martinique", "code" => "MQ"],
        ["id" => "qrCgmo89y8hAud3B", "name" => "Mauritania", "code" => "MR"],
        ["id" => "C7M0sa9NghHGz3_A", "name" => "Mauritius", "code" => "MU"],
        ["id" => "mTwT44YE3tmirniJ", "name" => "Mayotte", "code" => "YT"],
        ["id" => "O139-tc2gFpi4fMu", "name" => "Mexico", "code" => "MX"],
        ["id" => "tCwE2zupwALOSNTp", "name" => "Micronesia, Federated States Of", "code" => "FM"],
        ["id" => "2ETvFrk7Yl-50c8m", "name" => "Moldova, Republic Of", "code" => "MD"],
        ["id" => "PS6FZiAWsV5T94VY", "name" => "Monaco", "code" => "MC"],
        ["id" => "ZVN0gOuutH_0_2T9", "name" => "Mongolia", "code" => "MN"],
        ["id" => "xaswe06CerJBPycm", "name" => "Montenegro", "code" => "ME"],
        ["id" => "_lKkjF6hVluczvj7", "name" => "Montserrat", "code" => "MS"],
        ["id" => "OPYs-nfPI66Lrp4t", "name" => "Morocco", "code" => "MA"],
        ["id" => "9HPzE8jyq2NuTO2Y", "name" => "Mozambique", "code" => "MZ"],
        ["id" => "IBhu5bWiGo-WdcqM", "name" => "Myanmar", "code" => "MM"],
        ["id" => "ZeBDLRQzbKbkiJ3A", "name" => "Namibia", "code" => "NA"],
        ["id" => "cPahplm-ttOOwdNC", "name" => "Nauru", "code" => "NR"],
        ["id" => "et-3B_r2CSkp-SZ8", "name" => "Nepal", "code" => "NP"],
        ["id" => "XjmM-DkUsKfQrtaV", "name" => "Netherlands", "code" => "NL"],
        ["id" => "OXYNGXWiWMpabqOn", "name" => "New Caledonia", "code" => "NC"],
        ["id" => "l-B9DC-82BZf_Qhk", "name" => "New Zealand", "code" => "NZ"],
        ["id" => "ELGiQoosdVWnlpKC", "name" => "Nicaragua", "code" => "NI"],
        ["id" => "6332oIu4PZlE4i_N", "name" => "Niger", "code" => "NE"],
        ["id" => "Q_VjVpwaBRNRjb2F", "name" => "Nigeria", "code" => "NG"],
        ["id" => "beAM5_AK9-RxP9jR", "name" => "Niue", "code" => "NU"],
        ["id" => "b3GGvtVhKJdFytsq", "name" => "Norfolk Island", "code" => "NF"],
        ["id" => "zF0lxvz-8oE3gHuC", "name" => "Northern Mariana Islands", "code" => "MP"],
        ["id" => "8JMIjvVvIRa7rxpx", "name" => "Norway", "code" => "NO"],
        ["id" => "qwWaYKTa3wFHnqlb", "name" => "Oman", "code" => "OM"],
        ["id" => "uYihoMfeqDQ4637o", "name" => "Pakistan", "code" => "PK"],
        ["id" => "Yxv6goJKI2PbwG6x", "name" => "Palau", "code" => "PW"],
        ["id" => "CZLcUuarcFZD-Mxt", "name" => "Palestine, State Of", "code" => "PS"],
        ["id" => "kV-W7Tu2vkZk4ghV", "name" => "Panama", "code" => "PA"],
        ["id" => "LFIMRaIKmL6gD_w5", "name" => "Papua New Guinea", "code" => "PG"],
        ["id" => "4ZE6arFXrJoNdpYR", "name" => "Paraguay", "code" => "PY"],
        ["id" => "29wIi_ODgp-n7kxi", "name" => "Peru", "code" => "PE"],
        ["id" => "82341coaHfgBzEZp", "name" => "Philippines", "code" => "PH"],
        ["id" => "VkqKhKOqvDz9-Fwu", "name" => "Pitcairn", "code" => "PN"],
        ["id" => "oI0Wc7qXhFS_mHyE", "name" => "Poland", "code" => "PL"],
        ["id" => "q2ApFFn51gtLW1z5", "name" => "Portugal", "code" => "PT"],
        ["id" => "wQp-UpmLrjyN-q_y", "name" => "Puerto Rico", "code" => "PR"],
        ["id" => "CzIJ9TbtE_dIFn-Y", "name" => "Qatar", "code" => "QA"],
        ["id" => "mrEVf7aVn0N2DjD2", "name" => "Réunion", "code" => "RE"],
        ["id" => "RO2ErzVgUyvJ7t8q", "name" => "Romania", "code" => "RO"],
        ["id" => "MiNH2jaIK7UTmr11", "name" => "Russian Federation", "code" => "RU"],
        ["id" => "Be05CBUUDyqHpnnO", "name" => "Rwanda", "code" => "RW"],
        ["id" => "UYVEznNq6n4uIcZe", "name" => "Saint Barthélemy", "code" => "BL"],
        ["id" => "FNqUn0OWilkPOzU8", "name" => "Saint Helena, Ascension And Tristan Da Cunha", "code" => "SH"],
        ["id" => "jX-Twu8WA_sv8MRa", "name" => "Saint Kitts And Nevis", "code" => "KN"],
        ["id" => "BpUAUi3fd8tj92ic", "name" => "Saint Lucia", "code" => "LC"],
        ["id" => "2BSJmONFGulQ73sa", "name" => "Saint Martin (french Part)", "code" => "MF"],
        ["id" => "w-yGzRc8POxoVZB0", "name" => "Saint Pierre And Miquelon", "code" => "PM"],
        ["id" => "ArmgzcY_A931UW06", "name" => "Saint Vincent And The Grenadines", "code" => "VC"],
        ["id" => "bcXFtiHiTJyPSt9K", "name" => "Samoa", "code" => "WS"],
        ["id" => "r0CCsOJWFQSrUZzf", "name" => "San Marino", "code" => "SM"],
        ["id" => "tPO58uNuP1TBt_zS", "name" => "Sao Tome And Principe", "code" => "ST"],
        ["id" => "HAU4WAepX6adk_nm", "name" => "Saudi Arabia", "code" => "SA"],
        ["id" => "nB0uwQEDUQtJ383W", "name" => "Senegal", "code" => "SN"],
        ["id" => "EGAJPCTdc9TvK_XM", "name" => "Serbia", "code" => "RS"],
        ["id" => "wcABrMua1c4Apvqq", "name" => "Seychelles", "code" => "SC"],
        ["id" => "8zT_CCRsFa_6Joci", "name" => "Sierra Leone", "code" => "SL"],
        ["id" => "jaN_oyDdvxTHA8AL", "name" => "Singapore", "code" => "SG"],
        ["id" => "vJnfRO1RxhxS3_vK", "name" => "Sint Maarten (dutch Part)", "code" => "SX"],
        ["id" => "mUFjxFeCZfQVF5s0", "name" => "Slovakia", "code" => "SK"],
        ["id" => "PVQgRMLYLLdhA4hA", "name" => "Slovenia", "code" => "SI"],
        ["id" => "dR0Z6o0TF8cQWF05", "name" => "Solomon Islands", "code" => "SB"],
        ["id" => "eSOeVzaLFXt9F97r", "name" => "Somalia", "code" => "SO"],
        ["id" => "g4NMuEOyfooJcDSn", "name" => "South Africa", "code" => "ZA"],
        ["id" => "ysTOQQYWdd9kFSYg", "name" => "South Georgia And The South Sandwich Islands", "code" => "GS"],
        ["id" => "Aro85Z2HejcQq4Zw", "name" => "South Sudan", "code" => "SS"],
        ["id" => "KwdIx3DsNhoEHynE", "name" => "Spain", "code" => "ES"],
        ["id" => "Xhwb2iSmm6f73Lk1", "name" => "Sri Lanka", "code" => "LK"],
        ["id" => "IdsgmwOhe1kbsP-Z", "name" => "Sudan", "code" => "SD"],
        ["id" => "_q2z0k6uu-SzgUVc", "name" => "Suriname", "code" => "SR"],
        ["id" => "uEBr4HgXKFZ6dIX9", "name" => "Svalbard And Jan Mayen", "code" => "SJ"],
        ["id" => "nN_ifJraHwjsIAne", "name" => "Swaziland", "code" => "SZ"],
        ["id" => "t3t5QsB2JERRZfen", "name" => "Sweden", "code" => "SE"],
        ["id" => "kJV2NHQ2a0S3CrAH", "name" => "Switzerland", "code" => "CH"],
        ["id" => "mZB1DxhXkrlbluYs", "name" => "Syrian Arab Republic", "code" => "SY"],
        ["id" => "FX6C6a_kdZqSm4Eh", "name" => "Taiwan", "code" => "TW"],
        ["id" => "0W_qOYUcdI88kiGT", "name" => "Tajikistan", "code" => "TJ"],
        ["id" => "IVu926qR5Xc1-ccn", "name" => "Tanzania, United Republic Of", "code" => "TZ"],
        ["id" => "VCNeSv2VSaxdYnzb", "name" => "Thailand", "code" => "TH"],
        ["id" => "4Ybs91TfdfT5pKdh", "name" => "Timor-leste", "code" => "TL"],
        ["id" => "jefYI907W3ZR5MND", "name" => "Togo", "code" => "TG"],
        ["id" => "IKu8cL1UG2BpJ7MK", "name" => "Tokelau", "code" => "TK"],
        ["id" => "hkGSVOEvcCxODMze", "name" => "Tonga", "code" => "TO"],
        ["id" => "E9vAOhPxOSSxpHPL", "name" => "Trinidad And Tobago", "code" => "TT"],
        ["id" => "aQDt0J9nDPOZiARQ", "name" => "Tunisia", "code" => "TN"],
        ["id" => "lGSNBD54BICttyG8", "name" => "Turkey", "code" => "TR"],
        ["id" => "xnic6iHPMbXTyfUz", "name" => "Turkmenistan", "code" => "TM"],
        ["id" => "w3tlwNlUWZfMJ63w", "name" => "Turks And Caicos Islands", "code" => "TC"],
        ["id" => "5NcRejxK0ZKhZzGO", "name" => "Tuvalu", "code" => "TV"],
        ["id" => "f-75M1Tli49xsnMT", "name" => "Uganda", "code" => "UG"],
        ["id" => "C57E1KHoi-konMfF", "name" => "Ukraine", "code" => "UA"],
        ["id" => "mtgEY_-OhvTBShl9", "name" => "United Arab Emirates", "code" => "AE"],
        ["id" => "GchktFD95-9Rl7KR", "name" => "United Kingdom", "code" => "GB"],
        ["id" => "fc5Gr4K6SjWsMeux", "name" => "United States", "code" => "US"],
        ["id" => "PEpInywaT2Oh6M2J", "name" => "United States Minor Outlying Islands", "code" => "UM"],
        ["id" => "R5cX_6bU6pflh0FF", "name" => "Uruguay", "code" => "UY"],
        ["id" => "EY2Z2XGQumSDFB1P", "name" => "Uzbekistan", "code" => "UZ"],
        ["id" => "Yy9QV_cP8x-EhXne", "name" => "Vanuatu", "code" => "VU"],
        ["id" => "puX2VBT880w3xGbm", "name" => "Venezuela, Bolivarian Republic Of", "code" => "VE"],
        ["id" => "HSVMSL4CjWglRJ2s", "name" => "Viet Nam", "code" => "VN"],
        ["id" => "SBjdYoYp-r767cMI", "name" => "Virgin Islands, British", "code" => "VG"],
        ["id" => "XmezGSTFLKIURPuR", "name" => "Virgin Islands, U.s.", "code" => "VI"],
        ["id" => "-KvFaE3hSsqnJF63", "name" => "Wallis And Futuna", "code" => "WF"],
        ["id" => "33kNIWTaneNUkBdx", "name" => "Western Sahara", "code" => "EH"],
        ["id" => "YDt6pf76ldhOcWSB", "name" => "Kosovo", "code" => "KV"],
        ["id" => "RQnngXkCRuBcCZYw", "name" => "Yemen", "code" => "YE"],
        ["id" => "RQs-wBXDVlsGmaq8", "name" => "Zambia", "code" => "ZM"],
        ["id" => "l3pKu8YaidtQ_Psa", "name" => "Zimbabwe", "code" => "ZW"]
    );

    return $array_country;
}

function get_state_data()
{
    $array_state = array();
    $array_state['US'] =
        array(["id" => "2nOeRWrzq93rv4xH", "name" => "Alabama", "code" => "AL"],
            ["id" => "7nZhhtoWaU8v15aa", "name" => "Alaska", "code" => "AK"],
            ["id" => "2hcFvJjHv1FfLzRy", "name" => "Arizona", "code" => "AZ"],
            ["id" => "Vx3keSG_qIQfyQvr", "name" => "Arkansas", "code" => "AR"],
            ["id" => "GmUlbTYM8x2LM0CD", "name" => "California", "code" => "CA"],
            ["id" => "dJQ-UquCv9A_2Do0", "name" => "Colorado", "code" => "CO"],
            ["id" => "6cssXfGQHs3on3p-", "name" => "Connecticut", "code" => "CT"],
            ["id" => "968vvBy9S-XVE6sJ", "name" => "Delaware", "code" => "DE"],
            ["id" => "_TZyAPveftfKYyYf", "name" => "District of Columbia", "code" => "DC"],
            ["id" => "dzstGld9n_Q5MFXk", "name" => "Florida", "code" => "FL"],
            ["id" => "ViHjNjugBn7NVT0b", "name" => "Georgia", "code" => "GA"],
            ["id" => "Dw2eefDe2C_Z97-1", "name" => "Hawaii", "code" => "HI"],
            ["id" => "x5Lx40EfSMHdYyWi", "name" => "Idaho", "code" => "ID"],
            ["id" => "Um53SIodZ5_fDIAy", "name" => "Illinois", "code" => "IL"],
            ["id" => "vKzOYxvg8b0z5-4Q", "name" => "Indiana", "code" => "IN"],
            ["id" => "I8HU33il4EZYNCWQ", "name" => "Iowa", "code" => "IA"],
            ["id" => "kk72Rc95W3ieCowY", "name" => "Kansas", "code" => "KS"],
            ["id" => "q5-QyMDDGpaUEJUj", "name" => "Kentucky", "code" => "KY"],
            ["id" => "G4G-Wl9z_LSTYyxK", "name" => "Louisiana", "code" => "LA"],
            ["id" => "T5evKpNJQGgTv3W3", "name" => "Maine", "code" => "ME"],
            ["id" => "KfY4I2bJBf1h3PP9", "name" => "Maryland", "code" => "MD"],
            ["id" => "D6P2mwEyeoHd5FVC", "name" => "Massachusetts", "code" => "MA"],
            ["id" => "gq6rheSAoGD99u8L", "name" => "Michigan", "code" => "MI"],
            ["id" => "c5Xd774BRtzFcirT", "name" => "Minnesota", "code" => "MN"],
            ["id" => "QVvijTjli2xe7nYX", "name" => "Mississippi", "code" => "MS"],
            ["id" => "0sVrQdH_UIrGLCt2", "name" => "Missouri", "code" => "MO"],
            ["id" => "BtuFJCl7y0kmCO7g", "name" => "Montana", "code" => "MT"],
            ["id" => "1MoE2Sl_VQFB-_fv", "name" => "Nebraska", "code" => "NE"],
            ["id" => "qATHFEZ00sLgCi9Y", "name" => "Nevada", "code" => "NV"],
            ["id" => "zJfO8WSMOzCmvi-0", "name" => "New Hampshire", "code" => "NH"],
            ["id" => "mFugYHUfJs8qhIKv", "name" => "New Jersey", "code" => "NJ"],
            ["id" => "OjLpkm60ExoAiBPB", "name" => "New Mexico", "code" => "NM"],
            ["id" => "_x5kDlVKcwJNCBcA", "name" => "New York", "code" => "NY"],
            ["id" => "MM1Dm4LJ1J-xsB5e", "name" => "North Carolina", "code" => "NC"],
            ["id" => "yOk8B9SOqyPnF5RC", "name" => "North Dakota", "code" => "ND"],
            ["id" => "dUVkNO5DsjLzgZz3", "name" => "Ohio", "code" => "OH"],
            ["id" => "Da55ZsKm20WUr8Lb", "name" => "Oklahoma", "code" => "OK"],
            ["id" => "0H87odcgqajIDYpP", "name" => "Oregon", "code" => "OR"],
            ["id" => "qBOeRwKjMdkW5sCD", "name" => "Pennsylvania", "code" => "PA"],
            ["id" => "DqmQVKWALswS0_Ku", "name" => "Rhode Island", "code" => "RI"],
            ["id" => "lKcFDezmW8cr66Pt", "name" => "South Carolina", "code" => "SC"],
            ["id" => "mvea95AMqTM0vNhU", "name" => "South Dakota", "code" => "SD"],
            ["id" => "sDdQNLyPiXEeAOD7", "name" => "Tennessee", "code" => "TN"],
            ["id" => "k12mDTFfYyYx6dnQ", "name" => "Texas", "code" => "TX"],
            ["id" => "HA1Nh9Q6jARK3Kqn", "name" => "Utah", "code" => "UT"],
            ["id" => "2GdMkxmodRHrVS7C", "name" => "Vermont", "code" => "VT"],
            ["id" => "DvJSp0HOPT2Q2cSo", "name" => "Virginia", "code" => "VA"],
            ["id" => "xbtV8hiXrZHnjgAX", "name" => "Washington", "code" => "WA"],
            ["id" => "dDc0crY4LZ0lC2sn", "name" => "West Virginia", "code" => "WV"],
            ["id" => "-o86a5QaNwue02WM", "name" => "Wisconsin", "code" => "WI"],
            ["id" => "BBGjEzAe1xhte8Mm", "name" => "Wyoming", "code" => "WY"],
            ["id" => "-LfvrT1YmcjjHhxu", "name" => "APO AA", "code" => "AA"],
            ["id" => "aAx_o7DAsMscx0W8", "name" => "APO AP", "code" => "AP"],
            ["id" => "DL-7oTW7V9WMtzNz", "name" => "APO AE", "code" => "AE"],
            ["id" => "glPaf5tvlzljJ9bu", "name" => "American Samoa", "code" => "AS"],
            ["id" => "Aw1Cam9tenB0tW3h", "name" => "Guam", "code" => "GU"],
            ["id" => "yq33SVgC9PewEjtk", "name" => "Northern Mariana Islands", "code" => "MP"],
            ["id" => "Ias7Wt7mM8AkFX6E", "name" => "Puerto Rico", "code" => "PR"],
            ["id" => "D4kQ-G8jytyj_Qo8", "name" => "Virgin Islands", "code" => "VI"]
        );
    $array_state['CA'] =
        array(["id" => "jbwk4TVxnQ2g8vZD", "name" => "British Columbia", "code" => "BC"],
            ["id" => "_kjOdzT9hZjGXEwM", "name" => "Newfoundland", "code" => "NL"],
            ["id" => "NzhG5fy_5-R26HKC", "name" => "Manitoba", "code" => "MB"],
            ["id" => "egjFrPDBeLa51VAs", "name" => "New Brunswick", "code" => "NB"],
            ["id" => "xhNgsq_rAxH9ZDb_", "name" => "Nova Scotia", "code" => "NS"],
            ["id" => "jSzZOladHe10KoDG", "name" => "Nunavut", "code" => "NU"],
            ["id" => "3JNJuK47ET8fD4Ni", "name" => "Northwest Territories", "code" => "NT"],
            ["id" => "-l4GXuWpcCAdPLp3", "name" => "Ontario", "code" => "ON"],
            ["id" => "S-_uEP1yJOmTBP02", "name" => "Prince Edward Island", "code" => "PE"],
            ["id" => "o6kjCQ2_MkHapZss", "name" => "Quebec", "code" => "QC"],
            ["id" => "pS9eTUaGrble95cH", "name" => "Saskatchewen", "code" => "SK"],
            ["id" => "bPr-QG5CUPFdeUtH", "name" => "Yukon", "code" => "YT"]
        );
    $array_state['MX'] =
        array(["id" => "JMPzQ7bxCBTOkPCG", "name" => "Aguascalientes", "code" => "AG"],
            ["id" => "xK0Z20ETJv-SoLfr", "name" => "Baja California", "code" => "BN"],
            ["id" => "KaON3k4HHm_qSQPa", "name" => "Baja California Sur", "code" => "BS"],
            ["id" => "b4yxgcgUH3pNMlLe", "name" => "Campeche", "code" => "CM"],
            ["id" => "OwlEuoiqx5fvDOH7", "name" => "Chiapas", "code" => "CP"],
            ["id" => "z8Mf5fmgE10Vami1", "name" => "Chihuahua", "code" => "CH"],
            ["id" => "bvfW7xJ3ARt9zCmO", "name" => "Coahuila", "code" => "CA"],
            ["id" => "EOYqm_-PMVR8MyKh", "name" => "Colima", "code" => "CL"],
            ["id" => "etUmlIkIF7aZMjED", "name" => "Distrito Federal", "code" => "DF"],
            ["id" => "sblCxInRMhfagT1R", "name" => "Durango", "code" => "DU"],
            ["id" => "EY6rp3VSAwSYmVXA", "name" => "Estado de México", "code" => "MX"],
            ["id" => "xivCMF50YUg4cNRh", "name" => "Guanajuato", "code" => "GT"],
            ["id" => "r--tguUvYTln2P7w", "name" => "Guerrero", "code" => "GR"],
            ["id" => "sP1U_9ZDw126ubGD", "name" => "Hidalgo", "code" => "HI"],
            ["id" => "1rzXq-srPRUjN3Qf", "name" => "Jalisco", "code" => "JA"],
            ["id" => "UwkY7D0s5T9HEpNq", "name" => "Michoacán", "code" => "MC"],
            ["id" => "0zK80HAS3hRkXztU", "name" => "Morelos", "code" => "MR"],
            ["id" => "NTqqze6PMtMg6yKK", "name" => "Nayarit", "code" => "NA"],
            ["id" => "zSwr9_BQ4CZXkHRj", "name" => "Nuevo León", "code" => "NL"],
            ["id" => "-EI8ekogQd8EofPA", "name" => "Oaxaca", "code" => "OA"],
            ["id" => "TATOFY88jbCPE2qU", "name" => "Puebla", "code" => "PU"],
            ["id" => "u75UGwvcu2g_mA2P", "name" => "Querétaro", "code" => "QE"],
            ["id" => "CqqkFZh115lYJeUw", "name" => "Quintana Roo", "code" => "QR"],
            ["id" => "1NUClNwexkIUwgq0", "name" => "San Luis Potosí", "code" => "SL"],
            ["id" => "H_al9hyEZd8xFLkQ", "name" => "Sinaloa", "code" => "SI"],
            ["id" => "lKWSBBOz7yaQYKbE", "name" => "Sonora", "code" => "SO"],
            ["id" => "AJUdgipTTaTTDzmf", "name" => "Tabasco", "code" => "TB"],
            ["id" => "JndcSGdT9CJld9P8", "name" => "Tamaulipas", "code" => "TM"],
            ["id" => "xf6d8crn0AGzxqlQ", "name" => "Tlaxcala", "code" => "TL"],
            ["id" => "nUefPmKTRedzomSG", "name" => "Veracruz", "code" => "VE"],
            ["id" => "zK9k54hhyfPy9XZZ", "name" => "Yucatán", "code" => "YU"],
            ["id" => "GSmJFCxwsDImvtND", "name" => "Zacatecas", "code" => "ZA"]
        );

    return $array_state;
}

function secondsToTime($milliseconds)
{
    $seconds = floor($milliseconds / 1000);
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%ad %hh %im');
}

function getImgFromCampaign($camp)
{
    $product = null;
    foreach ($camp->products as $item) {
        if ($item->default) {
            $product = $item;
            break;
        }
    }

    if (!$product) {
        $product = $camp->products[0];
    }

    $variant = null;
    foreach ($product->variants as $item) {
        if ($item->default) {
            $variant = $item;
            break;
        }
    }
    if (!$variant) {
        $variant = $product->variants[0];
    }

    $res = [];
    if ($product->back_view) {
        $res['front'] = (isValidUrl($variant->image->back)) ? $variant->image->back : '';
        $res['back'] = (isValidUrl($variant->image->front)) ? $variant->image->front : '';
    } else {
        $res['front'] = (isValidUrl($variant->image->front)) ? $variant->image->front : '';
        $res['back'] = (isValidUrl($variant->image->back)) ? $variant->image->back : '';
    }

    //Get image _m
    if($res['front'] != '') $res['front'] = str_replace('.png','_m.png',$res['front']);
    if($res['back'] != '') $res['back'] = str_replace('.png','_m.png',$res['back']);

    return $res;
}

function genHtmlLoadFile($arrFileName, $type = 'js')
{
    $webpath = asset('/');
    $sResHtml = '';
    if ($type === 'js') {
        for ($index = 0; $index < sizeof($arrFileName); $index++) {
            $sResHtml .= '<script src="' . $webpath . $arrFileName[$index] . '?' . config('config.version') . '" type="text/javascript"></script>';
        }
    } else if ($type === 'css') {
        for ($index = 0; $index < sizeof($arrFileName); $index++) {
            $sResHtml .= '<link href="' . $webpath . $arrFileName[$index] . '?' . config('config.version') . ' " rel="stylesheet" />';
        }
    }
    return $sResHtml;
}

function isValidUrl($url)
{
    return isset($url) && $url !== '';
}