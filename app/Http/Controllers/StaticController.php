<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Core\app\Models\Comment;
use TechStudio\Lms\app\Models\Course;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use TechStudio\Core\app\Exports\CommentsExport;
use TechStudio\Core\app\Models\UserProfile;
use Maatwebsite\Excel\Facades\Excel;
use TechStudio\Core\app\Http\Resources\CommentsArticleResource;
use TechStudio\Core\app\Http\Resources\StaticResource;
use TechStudio\Core\app\Http\Resources\StaticsResource;
use TechStudio\Core\app\Models\Statics;

class StaticController extends Controller
{
    public function index() 
    {
        $index = Statics::get();    
        return StaticResource::collection($index);
    }

    public function list() 
    {
        $list = Statics::paginate(10);
        return new StaticsResource($list);
    }

    public function createUpdate(Request $request)
    {
        $static = Statics::updateOrCreate(
            ['id' => $request['id']],
            [
                'key' => $request['key'],
                'title' => $request['title'],
                'text' => $request['text'],
                'file_url' => $request['fileUrl'],
                ]
            );
            
        return new StaticResource($static);
    }

    public function delete($locale, $id)
    {
        $static = Statics::where('id', $id)->firstOrFail();
        $static->delete();
        return response()->json([
            'message' => 'با موفقیت حذف شد!'
        ], 200);
    }
}
