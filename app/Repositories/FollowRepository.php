<?php

namespace TechStudio\Lms\app\Repositories;

use Illuminate\Support\Facades\Auth;
use TechStudio\Core\app\Models\Follow;
use TechStudio\Lms\app\Repositories\Interfaces\FollowRepositoryInterface;

// use TechStduio\Lms\app\Repositories\Interface

class FollowRepository implements FollowRepositoryInterface
{
    public function storeRemove($request)
    {
        $user = auth()->user();
        $followId = $request->followId;

        if ($request->do == 'follow') {
            Follow::create([
                'follower' => $user->id, 
                'following' => $followId
            ]
        );
            return response()->json([
                'status' => 'success',
                'message' => 'کاربر مورد نظر فالو شد.'
            ], 200);
        }

        if ($request->do == 'unFollow') {
            $follow = Follow::where('follower', $user->id)
            ->where('following', $followId)
            ->firstOrFail();

            $follow->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'کاربر مورد نظر آنفالو شد.'
            ], 200);
        }
    }

    
}
