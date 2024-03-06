<?php

namespace TechStudio\Core\app\Helper;

use getid3;
use Carbon\Exceptions\Exception;
use Illuminate\Support\Facades\Storage;


class ContentDuration
{

    public static function text($content)
    {
        $duration = 0;
        foreach ($content as $cont) {
            $duration += HtmlContent::minutesToRead(json_encode($cont['content']));
        }
        return round($duration);
    }

    public static function video($content)
    {
        // $contentDuration = null;
        // $getID3 = new \getID3;
        // foreach ($content as $video) {
        //     $tempFileName = tempnam("/tmp", "video-file-");
        //     try {
        //         $ch = curl_init($video['content']['url']);
        //         curl_setopt($ch, CURLOPT_NOBODY, true);
        //         curl_exec($ch);
        //         $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //         curl_close($ch);
        //         if ($responseCode == 200) {
        //             copy($video['content']['url'], $tempFileName);
        //             $result = $getID3->analyze($tempFileName);
        //             unlink($tempFileName);
        //             $contentDuration += strtotime($result['playtime_string']);
        //         }
        //     } catch (Exception $e) {
        //         return 0;
        //     }
        // }
        // return gmdate('H', $contentDuration);
        return rand(15,40);
    }

    public static function exam($content)
    {
        $questions = 0;
        foreach ($content as $cont) {
            $questions += count($cont['content']['questions']);
        }
        return $questions;
    }
}
