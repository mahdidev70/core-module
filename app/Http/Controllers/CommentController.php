<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Core\app\Models\Comment;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CommentController extends Controller
{
//     public function store(Article $slug ,Request $request)
//     {
//         $validatedData = $request->validate([
//             'text'=>['required', 'max:600'],
//         ]);
//         $ip = $request->header('X-Real-IP') ?? $request->ip();
//         $input = $request->all();
//         $input['user_id'] = Auth::user()->id;
//         $input['user_type'] = get_class(Auth::user());
//         $input['commentable_type'] = get_class($slug);
//         $input['commentable_id'] = $slug->id;
//         $input['ip'] =  $ip;
//         $input['parent_id'] = (isset($request->replyTo) && $request->replyTo !=0 ) ?$request->replyTo : null;

// return        $comment = Comment::create($input);
//         $result['id'] = $comment->id;
//         $result['text'] = $comment->text;
//         $result['creationDate'] = $comment->created_at;
//         $result['user'] =  [
//         "displayName" =>  $comment->user->getDisplayName(),
//         "id"=> $comment->user->id,
//         "avatarUrl" => $comment->user->avatar_url
//     ];
//         $result['replies'] = [];
//         $result['feedback'] = [
//             'likesCount' => 0,"dislikesCount" => 0 , "currentUserAction" => null
//         ];

//         return response()->json($result,200);
//     }

//     public function getComments(Article $slug, Request $request)
//     {
//         $article = new Article();

//         $parentId = $request->parentId;
//         if ($parentId) {
//             $commentsQuery = Comment::where('commentable_type', get_class($article))
//             ->where('parent_id', $parentId)->where('status','approved');
//         } else {
//             $commentsQuery = $slug->comments();
//         }
//         $commentsQuery = $commentsQuery->withCount('replies');

//         $comments = $commentsQuery->orderByDesc('created_at')->with('replies')->paginate(6)->through(
//             fn($comment) => [
//             "id" => $comment->id,
//             "user" => [
//                 "displayName" => $comment->user->getDisplayName(),
//                 "id"=> $comment->user->id,
//                 "avatarUrl" => $comment->user->avatar_url
//             ],
//             "creationDate" => $comment->created_at,
//             "text" => $comment->text,
//             "feedback" => [
//                 'likesCount' => $comment->likes_count??0,
//                 'dislikesCount' => $comment->dislikes_count??0,
//                 'currentUserAction' => $comment->current_user_feedback()?? null,
//             ],
//             "replies" => array_values($comment->replies->sortByDesc('created_at')->take(6)->map(fn($reply)=>[
//                 "id" => $reply->id,
//                 "user" => [
//                     "displayName" => $reply->user->getDisplayName(),
//                     "id"=> $reply->user->id,
//                     "avatarUrl" => $reply->user->avatar_url
//                 ],
//                 "text" => $reply->text,
//                 "creationDate" => $reply->created_at,
//                 "feedback" => [
//                     'likesCount' => $reply->likes_count??0,
//                     'dislikesCount' => $reply->dislikes_count??0,
//                     'currentUserAction' => $reply->current_user_feedback()?? null,
//                 ],
//             ])->toArray()),
//             "replyCount" => $comment->replies_count,
//             "replyLastPage" =>  floor(($comment->replies_count-1) / 6) + 1,
//         ]);
//         return $comments;
//     }

//     public function storeFeedback($article_slug,$commentId,Request $request)
//     {
//         if (!$request->has('action') || !in_array($request->action,['clear', 'like', 'dislike'])){
//             throw new BadRequestException("'action' request data field must be either of [clear, like, dislike]."); // improve validation
//         }
//         $comment_query = Comment::where('id', $commentId)->firstOrFail();
//         $currentUserAction = $request->action;
//         // likeBy() or dislikeBy() or clearBy
//         $functionName = strtolower($request->action).'By';
//         $comment_query->$functionName(Auth::user()->id);
//         return [
//             'feedback' => [
//                 'likesCount' => $comment_query->likes_count??0,
//                 'dislikesCount' => $comment_query->dislikes_count??0,
//                 'currentUserAction' => $currentUserAction,
//             ],
//         ];


//     }


    // public function getArticleCommentsListData(Request $request)
    // {

    //     $article = new Article();

    //     $query = Comment::where('commentable_type', get_class($article))->with('user', 'article');

    //     if ($request->filled('search')) {
    //         $txt = $request->get('search');

    //         $query->where(function ($q) use ($txt) {
    //             $q->where('text', 'like', '%' . $txt . '%');
    //         });
    //     }

    //     if ($request->filled('status')) {
    //         if ($request->status == 'deleted'){
    //             $query->where('status', 'deleted');
    //         }elseif ($request->status == 'rejected') {
    //             $query->where('status', 'rejected');
    //         }elseif ($request->status == 'waiting_for_approval') {
    //             $query->where('status' == 'waiting_for_approval');
    //         }elseif ($request->status == 'approved') {
    //             $query->where('status', 'approved');
    //         }
    //     }

    //     $comments = $query->paginate(10);

    //     $data = $comments->map(function ($comment) {
    //         return [
    //             'author' => [
    //                 'displayName' => $comment->user->getDisplayName(),
    //                 'id' => $comment->user->id,
    //             ],
    //             'text' => $comment->text,
    //             'relatedArticle' => [
    //                 'title' => $comment->article->title,
    //                 'slug' => $comment->article->slug,
    //             ],
    //             'date' => $comment->created_at,
    //             'ip' => $comment->ip,
    //             'status' => $comment->status,
    //             'id'=> $comment->id
    //         ];
    //     });

    //     return [
    //         'total' => $comments->total(),
    //         'current_page' => $comments->currentPage(),
    //         'per_page' => $comments->perPage(),
    //         'last_page' => $comments->lastPage(),
    //         'data' => $data
    //     ];

    // }

    public function getArticleCommentsListCommon()
    {
        $article = new Article();

        return ['counts' => [
            'waitingForApproval' => Comment::where('commentable_type', get_class($article))->
            where('status', 'waiting_for_approval')->count(),
            'approved' => Comment::where('commentable_type', get_class($article))->where('status', 'approved')->count(),
            'rejected' => Comment::where('commentable_type', get_class($article))->where('status', 'rejected')->count(),
            'deleted' => Comment::where('commentable_type', get_class($article))->where('status', 'deleted')->count(),
            'withReplies' => Comment::where('commentable_type', get_class($article))->has('replies')->count(),
        ]
            ];
    }

    public function updateArticleCommentsStatus(Request $request, Comment $comment): array
    {
        $validatedData = $request->validate([
            'status' => 'required|in:approved,deleted,rejected',
            'ids' => 'required|array',
            'reason' => 'required_if:status,rejected'
        ]);

        $mapping = array(
            "apple" => "star",
            'approved' =>  "approved",
            'deleted'=>'deleted',
            'rejected' => 'rejected'
        );

        $data = [];
        $data['status'] =$mapping[$validatedData['status']];

        if ($mapping[$validatedData['status']] == 'rejected') {
            $data['rejection_reason'] = $validatedData['reason'];
        }

        Comment::whereIn('id', $validatedData['ids'])
            ->update($data);

        return [
            'updatedComments' => $validatedData['ids'],
        ];
    }

    // public function editArticleCommentText(Request $request, Comment $comment_id)
    // {
    //     return $comment_id;
    //     $comment_id->update(['text' => $request->text]);
    //     return ['id'=> $comment_id->id ];
    // }


    // public function getCourseCommnetsList(Request $request)
    // {
    //     $query = Comment::where('commentable_type', 'App\Models\Course');
    
    //     if ($request->filled('search')) {
    //         $txt = $request->get('search');
        
    //         $query->where(function ($q) use ($txt) {
    //             $q->where('text', 'like', '%' . $txt . '%');
    //         });
    //     }

    //     if ($request->filled('status')) {
    //         if ($request->status == 'deleted'){
    //             $query->where('status', 'deleted');
    //         }elseif ($request->status == 'rejected') {
    //             $query->where('status', 'rejected');
    //         }elseif ($request->status == 'waiting_for_approval') {
    //             $query->where('status' == 'waiting_for_approval');
    //         }elseif ($request->status == 'approved') {
    //             $query->where('status', 'approved');
    //         }
    //     }
    
    //     $comments = $query->with('commentable')->paginate(10);
    
    //     $commentData = $comments->map(function ($comment) {
    //         return [
    //             'id' => $comment->id,
    //             'user' => [
    //                 'id' => $comment->user->id,
    //                 'displayName' => $comment->user->getDisplayName(),
    //             ],
    //             'course' => [
    //                 'id' => $comment->commentable->id,
    //                 'title' => $comment->commentable->title,
    //             ],
    //             'text' => $comment->text,
    //             'status' => $comment->status,
    //             'rate' => $comment->star,
    //             'created_at' => $comment->created_at,
    //         ];
    //     });
    
    //     $response = [
    //         'total' => $comments->total(),
    //         'per_page' => $comments->perPage(),
    //         'current_page' => $comments->currentPage(),
    //         'last_page' => $comments->lastPage(),
    //         'data' => $commentData,
    //     ];
    
    //     return $response;
    // }

//     public function editCreateCommentCourse(Request $request)
//     {

//         $userType = ''; 

//         if ($request['user'][0]['type'] == 'user') {
//             $userType = 'App\Models\UserProfile';
//         }elseif ($request['user'][0]['type'] == 'alias') {
//             $userType = 'App\Models\Alias';
//         }

//         $comment = Comment::updateOrCreate(
//             ['id' => $request['id']],
//             [
//                 'user_id' => $request['user'][0]['id'],
//                 'user_type' => $userType,
//                 'commentable_type' => 'App\Models\Course',
//                 'commentable_id' => $request['courseId'],
//                 'star' => $request['rate'],
//                 'text' => $request['text'],
//             ]
//         );

//         return $comment->id;
//     }

//     public function getCourseCommonList()
//     {
//         $counts = Comment::where('commentable_type', 'App\Models\Course');

//         $counts = [
//             'all' => $counts->count(),
//             'waiting_for_approval' => $counts->where('status', 'waiting_for_approval')->count(),
//             'delete' => $counts->where('status', 'deleted')->count(),
//             'rejected' => $counts->where('status', 'rejected')->count(),
//             'approved' => $counts->where('status', 'approved')->count(),
//         ];

//         return $counts;
//     }
}
