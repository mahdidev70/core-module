<?php

namespace TechStudio\Core\app\Models\Traits;

use TechStudio\Core\app\Models\File;

trait Attachable
{
    public function associateAttachments($ids)
    {
        $result= [];
        foreach ($ids as $id) {
            $file = File::find($id);
            if ($file->user_id !== \Auth::user()->id){
                throw new \ErrorException('Error found! Files are not Correct');
            }
            $this->attachments()->save($file);
            $result[] = ["id"=>$file['id'],"previewImageUrl"=>$file['file_url']];
        }
        return $result;
    }

    public function attachments()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
