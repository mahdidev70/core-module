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

class CommentController extends Controller
{
    protected function getCommentsData($slug, $request, $modelClass)
    {
        $parentId = $request->parentId;
        if ($parentId) {
            $commentsQuery = Comment::where('commentable_type', $modelClass)
                ->where('parent_id', $parentId)
                ->where('status', 'approved');
        } else {
            $commentsQuery = $slug->comments();
        }

        $commentsQuery = $commentsQuery->withCount('replies');

        $ArticleComments = $commentsQuery->orderByDesc('created_at')
            ->with('replies')
            ->paginate(6)
            ->through(function($comment) {
                return [
                    "id" => $comment->id,
                    "user" => [
                        "displayName" => $comment->user->getDisplayName(),
                        "id" => $comment->user->id,
                        "avatarUrl" => $comment->user->avatar_url
                    ],
                    "creationDate" => $comment->created_at,
                    "text" => $comment->text,
                    "status" => $comment->status,
                    "feedback" => [
                    'likesCount' => $comment->likes_count ?? 0,
                        'dislikesCount' => $comment->dislikes_count ?? 0,
                        'currentUserAction' => $comment->current_user_feedback() ?? null,
                    ],
                    "replies" => array_values($comment->replies->sortByDesc('created_at')->take(6)->map(function ($reply) {
                        return [
                            "id" => $reply->id,
                            "user" => [
                                "displayName" => $reply->user->getDisplayName(),
                                "id" => $reply->user->id,
                                "avatarUrl" => $reply->user->avatar_url
                            ],
                            "text" => $reply->text,
                            "creationDate" => $reply->created_at,
                            "feedback" => [
                                'likesCount' => $reply->likes_count??0,
                                'dislikesCount' => $reply->dislikes_count??0,
                                'currentUserAction' => $reply->current_user_feedback()?? null,
                            ],
                        ];
                    })->toArray()),
                    "replyCount" => $comment->replies_count,
                    "replyLastPage" => floor(($comment->replies_count - 1) / 6) + 1,
                ];
            });
        $userComments = null;
        
        if (Auth('sanctum')->user()) {
            $userComments = Comment::where('commentable_type', $modelClass)
                ->where('user_id', Auth('sanctum')->user()->id)
                ->where('status', 'waiting_for_approval')
                ->with('replies')
                ->latest('created_at')
                ->paginate(10)
                ->through(function ($comment) {
                    return [
                        "id" => $comment->id,
                        "user" => [
                            "displayName" => $comment->user->getDisplayName(),
                            "id" => $comment->user->id,
                            "avatarUrl" => $comment->user->avatar_url
                        ],
                        "creationDate" => $comment->created_at,
                        "text" => $comment->text,
                        "status" => $comment->status,
                    ];
                });
        }

        if (!$userComments){
            $comments = $ArticleComments;
        }else{
            $comments = $userComments->concat($ArticleComments);
        }

        return $comments;
    }
    public function store($local, $slug ,Request $request)
    {
        $slug = Article::where('slug', $slug)->where('language', $local)->firstOrFail();
        $validatedData = $request->validate([
            'text'=>['required', 'max:600'],
        ]);
        $ip = $request->header('X-Real-IP') ?? $request->ip();
        $input = $request->all();
        $input['user_id'] = auth()->user()->id;
        $input['user_type'] = get_class(auth()->user());
        $input['commentable_type'] = get_class($slug);
        $input['commentable_id'] = $slug->id;
        $input['ip'] =  $ip;
        $input['status'] = 'waiting_for_approval';
        $input['parent_id'] = (isset($request->replyTo) && $request->replyTo !=0 ) ?$request->replyTo : null;
        $comment = Comment::create($input);
        return response()->json($comment,200);
    }

    public function getComments($local, $slug, Request $request)
    {
        $articleSlug = Article::where('slug', $slug)->where('language', $local)->firstOrFail();
        $comments = $this->getCommentsData($articleSlug, $request, Article::class);
        return $comments;
    }

    public function storeFeedback($local, $slug, $commentId,Request $request)
    {
        $articleSlug = Article::where('slug', $slug)->firstOrFail();

        if (!$request->has('action') || !in_array($request->action,['clear', 'like', 'dislike'])){
            throw new BadRequestException("'action' request data field must be either of [clear, like, dislike]."); // improve validation
        }
        $comment_query = Comment::where('id', $commentId)->firstOrFail();
        $currentUserAction = $request->action;
        // likeBy() or dislikeBy() or clearBy
        $functionName = strtolower($request->action).'By';
        $comment_query->$functionName(auth()->user()->id);
        // return $comment_query;
        return [
            'feedback' => [
                'likesCount' => $comment_query->likes_count??0,
                'dislikesCount' => $comment_query->dislikes_count??0,
                'currentUserAction' => $currentUserAction,
            ],
        ];
    }

    public function getArticleCommentsListData(Request $request)
    {
        $article = new Article();
        $query = Comment::where('commentable_type', get_class($article))->with('user', 'article');

        if ($request->filled('search')) {
            $txt = $request->get('search');
            $query->where(function ($q) use ($txt) {
                $q->where('text', 'like', '%' . $txt . '%');
            });
        }

        if (isset($request->status) && $request->status != null) {
            $query->where('status', $request->input('status'));
        }

        $comments = $query->orderByDesc('created_at')->paginate(10);

        $data = $comments->map(function ($comment) {
            return [
                'author' => [
                    'displayName' => $comment->user->getDisplayName(),
                    'id' => $comment->user->id,
                ],
                'text' => $comment->text,
                'relatedArticle' => [
                    'title' => $comment->article? $comment->article->title:null,
                    'slug' => $comment->article?$comment->article->slug:null,
                ],
                'date' => $comment->created_at,
                'ip' => $comment->ip,
                'status' => $comment->status,
                'id'=> $comment->id
            ];
        });

        return [
            'total' => $comments->total(),
            'current_page' => $comments->currentPage(),
            'per_page' => $comments->perPage(),
            'last_page' => $comments->lastPage(),
            'data' => $data
        ];

    }

    public function getArticleCommentsListCommon()
    {
        $articleModel = new Article();

        $counts = [
            'waitingForApproval' => Comment::where('commentable_type', get_class($articleModel))->where('status', 'waiting_for_approval')->count(),
            'approved' => Comment::where('commentable_type', get_class($articleModel))->where('status', 'approved')->count(),
            'rejected' => Comment::where('commentable_type', get_class($articleModel))->where('status', 'rejected')->count(),
            'deleted' => Comment::where('commentable_type', get_class($articleModel))->where('status', 'deleted')->count(),
            'withReplies' => Comment::where('commentable_type', get_class($articleModel))->has('replies')->count(),

        ];

        $status = ['waiting_for_approval', 'approved', 'rejected', 'deleted'];

        return [
            'counts' => $counts,
            'status' => $status,
        ];
    }

    public function updateArticleCommentsStatus($local, Comment $comment, Request $request)
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

        public function editArticleCommentText($local, Comment $comment_id, Request $request)
        {
            $comment_id->update(['text' => $request->text]);
            return ['id'=> $comment_id->id ];
        }


        public function getCourseCommnetsList(Request $request)
        {
            $courseModel = new Course();

        $query = Comment::where('commentable_type', get_class($courseModel));

        if ($request->filled('search')) {
            $txt = $request->get('search');

            $query->where(function ($q) use ($txt) {
                $q->where('text', 'like', '%' . $txt . '%');
            });
        }

        if (isset($request->status) && $request->status != null) {
            $query->where('status', $request->input('status'));
        }

        $sortOrder= 'desc';
        if (isset($request->sortOrder) && ($request->sortOrder ==  'asc' || $request->sortOrder ==  'desc')) {
            $sortOrder = $request->sortOrder;
        }

        if ($request->has('sortKey')) {
            if ($request->sortKey == 'rate') {
                $query->orderBy('star', $sortOrder);
            }
        }else{
            $query->orderBy('created_at', $sortOrder);
        }

        $comments = $query->with('commentable')->paginate(10);

        $commentData = $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'author' => [
                    'id' => $comment->user->id,
                    'displayName' => $comment->user->getDisplayName(),
                ],
                'relatedCourse' => [
                    'title' => $comment->commentable ? $comment->commentable->title:null,
                    'slug' => $comment->commentable ? $comment->commentable->slug:null,
                ],
                'text' => $comment->text,
                'status' => $comment->status,
                'rate' => $comment->star,
                'date' => $comment->created_at,
                'ip' => $comment->ip,
            ];
        });

        $response = [
            'total' => $comments->total(),
            'per_page' => $comments->perPage(),
            'current_page' => $comments->currentPage(),
            'last_page' => $comments->lastPage(),
            'data' => $commentData,
        ];

        return $response;
    }

    public function editCreateCommentCourse(Request $request)
    {
        $userType = '';
        $userModel = new UserProfile();
        $courseModel = new Course();

        if ($request['user'][0]['type'] == 'user') {
            $userType = get_class($userModel);
        }

        $comment = Comment::updateOrCreate(
            ['id' => $request['id']],
            [
                'user_id' => $request['user'][0]['id'],
                'user_type' => $userType,
                'commentable_type' => get_class($courseModel),
                'commentable_id' => $request['courseId'],
                'star' => $request['rate'],
                'text' => $request['text'],
            ]
        );

        return $comment->id;
    }

    public function getCourseCommonList()
    {
        $courseModel = new Course();

        $counts = [
            'all' => Comment::where('commentable_type', get_class($courseModel))->count(),
            'waiting_for_approval' => Comment::where('commentable_type', get_class($courseModel))->where('status', 'waiting_for_approval')->count(),
            'delete' => Comment::where('commentable_type', get_class($courseModel))->where('status', 'deleted')->count(),
            'rejected' => Comment::where('commentable_type', get_class($courseModel))->where('status', 'rejected')->count(),
            'approved' => Comment::where('commentable_type', get_class($courseModel))->where('status', 'approved')->count(),
        ];

        $status = ['approved','waiting_for_approval','deleted','rejected'];

        return [
            'counts' => $counts,
            'status' => $status,
        ];;
    }


    public function updateCommentsStatus(Request $request, Comment $comment): array
    {
        $validatedData = $request->validate([
            'status' => 'required|in:approved,deleted,rejected',
            'ids' => 'required|array',
            'reportId' => 'required_if:status,rejected|exists:reports,id'
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
            $data['report_id'] = $validatedData['reportId'];
        }

        Comment::whereIn('id', $validatedData['ids'])
            ->update($data);

        return [
            'updatedComments' => $validatedData['ids'],
        ];
    }

    public function commentExport()
    {
        $courseModel = new Course();

        $query = Comment::where('commentable_type', get_class($courseModel));

        $comments = $query->with('commentable')->paginate(10);

        $commentData = $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'displayName' => $comment->user->getDisplayName(),
                'title' => $comment->commentable->title,
                'text' => $comment->text,
                'status' => $comment->status,
                'rate' => $comment->star,
                'date' => $comment->created_at,
                'ip' => $comment->ip,
            ];
        });

        $response = [
            'total' => $comments->total(),
            'per_page' => $comments->perPage(),
            'current_page' => $comments->currentPage(),
            'last_page' => $comments->lastPage(),
            'data' => $commentData,
        ];

        return $response;
    }

    public function exportExcel(Request $request)
    {
        $comments = $this->commentExport($request);
        return Excel::download(new CommentsExport($comments), 'comments.xlsx');
    }

    public function getUserComment(Request $request)
    {
        $articleModel = new Article();
        $comments = Comment::where('commentable_type', get_class($articleModel));

        $user = auth()->user();

        if ($request['data'] === 'their') {

            $articleIds = Article::where('author_id', $user->id)->orderBy('created_at', 'DESC')->pluck('id');
            $theirCommets = $comments->whereIn('commentable_id', $articleIds)->paginate(10);
            return new CommentsArticleResource($theirCommets);

        }elseif ($request['data'] === 'my') {

            $myComments = $comments->where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate(10);
            return new CommentsArticleResource($myComments);

        }

    }

}
