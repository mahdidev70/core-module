<?php

namespace TechStudio\Core\app\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class VideoController extends Controller
{

    public function list(Request $request)
    {
        $channel = env('ARVAN_CHANNEL_ID');
        $api_key = env('ARVAN_API_KEY');
        $headers = [
            'Authorization' =>  $api_key
        ];
        $page = 1;
        if ($request->get('page') > 1) {
            $page = $request->get('page');
        }
        $url = "https://napi.arvancloud.ir/vod/2.0/channels/{$channel}/videos" . "?page={$page}";

        $client = new Client(['headers' => $headers]);
        $res = $client->get($url, []);
        $res->getStatusCode();
        return json_decode($res->getBody());
    }

    public function search(Request $request)
    {
        $channel = env('ARVAN_CHANNEL_ID');
        $api_key = env('ARVAN_API_KEY');
        $headers = [
            'Authorization' =>  $api_key
        ];

        $page = 1;
        if ($request->get('page') > 1) {
            $page = $request->get('page');
        }
        $keyword = $request->get('keyword');
        $url = "https://napi.arvancloud.ir/vod/2.0/videos/search?filters%5Bchannel_id%5D=in%28{$channel}%29&filters%5Btitle%5D=like%28{$keyword}%29&sorts=-created_at&page={$page}&per_page=9";

        $client = new Client(['headers' => $headers]);
        $res = $client->get($url, []);
        $res->getStatusCode();
        return json_decode($res->getBody());
    }

    public function show(Request $request)
    {
        $api_key = env('ARVAN_API_KEY');
        $headers = [
            'Authorization' =>  $api_key
        ];
        $id = $request->get('id');
        $url = "https://napi.arvancloud.ir/vod/2.0/videos/{$id}";

        $client = new Client(['headers' => $headers]);
        $res = $client->get($url, []);
        $res->getStatusCode();
        return json_decode($res->getBody());
    }
}
