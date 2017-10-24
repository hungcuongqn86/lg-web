<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Http;

class RedisUpdateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:psubscripe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update redis data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Redis::psubscribe(['__keyevent*__:expired'], function ($message) {
            $redis = Redis::connection('connection2');
            if($message == 'category-list'){ //category list
                $category_response = Http::get(config('config.pspApiPath').'/categories');
                foreach($category_response->categories as $item){
                    $redis->set($item->url,json_encode($item));
                    $redis->expire($item->url,60*60*24);
                    $redis->lpush("category-list", $item->url);

                    $subcategory_response = Http::get(config('config.pspApiPath').'/categories', ['parent_id' => $item->id]);
                    foreach($subcategory_response->categories as $subitem){
                            $redis->set($subitem->url,json_encode($subitem));
                            $redis->expire($subitem->url,60*60*24);
                            $redis->lpush("category-list", $subitem->url);
                    }
                }
                $redis->expire("category-list",60*60*24);
                Log::info('redis:psubscripe update redis data for key:'.$message);
            }
            elseif($message == 'more-campaigns'){ // more campaign
                //More campaign
                //Hard code category id
                $cat_more = '1V84sYfazfHFMilr';
                
                //Get campaign data
                $campaign_more = Http::get(config('config.pspApiPath').'/campaigns', ['state' => 'launching','categories'=>$cat_more,'page'=>1,'page_size'=>4]);
                $campaign_more = extendCampaignObj($campaign_more->campaigns);
                
                $redis->set("more-campaigns",json_encode($campaign_more));
                $redis->expire("more-campaigns",60*10);
                Log::info('redis:psubscripe update redis data for key:'.$message);
            }
            elseif(strpos($message, '?page=') != false){ //campaign list depend on category
                $url = parse_url($message);
                $cat_url = str_replace('/shop','',$url['path']);
                $page = str_replace('page=','',$url['query']);
                $category_selected = json_decode($redis->get($cat_url));

                //Get campaign list
                $campaign_list = Http::get(config('config.pspApiPath').'/campaigns', ['state' => 'launching','categories' => $category_selected->id,'page'=>$page,'page_size'=>12]);
                $campaign_data = array("total"  => $campaign_list->total,
                                       "data"   => extendCampaignObj($campaign_list->campaigns));
                
                $redis->set($message,json_encode($campaign_data));
                $redis->expire($message,60*10);
                Log::info('redis:psubscripe update redis data for key:'.$message);
            }
        });
        
    }
}
