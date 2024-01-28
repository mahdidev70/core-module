<?php

namespace TechStudio\Core\app\Services\File;

use App\Services\FileService as ServicesFileService;
use TechStudio\Core\app\Models\File;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileService
{
    public function upload(
        Request $request,
        $max_count,
        $max_size_mb,
        $types,
        $format_result_as_attachment = false,
        $storage_key = 'community'
    ) {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|max:' . $max_count,
            'files.*' => [\Illuminate\Validation\Rules\File::types($types)->max(round(1024 * $max_size_mb))],
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $files = $request->file('files');
        $createdFiles = [];
        foreach ($files as $key => $file) {
            $fileObject = new File();
            // $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            // Storage::disk($storage_key)->put($fileName, file_get_contents($file));
            // $url = Storage::disk($storage_key)->url($fileName);
            if($file->extension() == 'mp4'){
                $url = FileService::uploadVideo($file);
            }else{
                $url = ServicesFileService::upload($file, $storage_key);
            }
            $fileObject->file_url = $url;
            $fileObject->user_id = Auth::user()->id;
            $fileObject->save();
            if ($format_result_as_attachment) {
                $createdFiles[] = [
                    'id' => $fileObject->id,
                    'type' => 'image',  // TODO: infer, refactor
                    'previewImageUrl' => $url,
                    'contentUrl' => $url,
                ];
            } else {
                $createdFiles[] = [
                    'id' => $fileObject->id,
                    'url' => $url,
                ];
            }
        }

        return $createdFiles;
    }

    public function uploadOneFile(Request $request, $storage_key = 'community')
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|max:2048|mimes:jpeg,png,jpg,gif',
        ]);
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        $file = $request->file('file');
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk($storage_key)->put($fileName, file_get_contents($file));
        $url = Storage::disk($storage_key)->url($fileName);
        return [
            'url' => $url,
        ];
    }
}
