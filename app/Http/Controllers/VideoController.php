<?php

namespace TechStudio\Core\app\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;


class VideoController extends Controller
{

    public function list(Request $request)
    {
        $channel = env('ARVAN_CHANNEL_ID');
        $api_key = env('ARVAN_API_KEY');
        // $headers = [
        //     'Authorization' =>  $api_key
        // ];
        $headers[] = 'Authorization: ' . $api_key;
        $page = 1;
        if ($request->get('page') > 1) {
            $page = $request->get('page');
        }
        $url = "https://napi.arvancloud.ir/vod/2.0/channels/{$channel}/videos" . "?page={$page}";

        try {
            $result = Cache::remember(
                'arvan-video-' . $page,
                env('MID_TIME', 720),
                function () use ($url, $headers) {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_VERBOSE, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    return $response;
                }
            );
            return json_decode($result);
        } catch (Exception $e) {
            return $e;
        }
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
