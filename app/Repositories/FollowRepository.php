<?php

namespace TechStudio\Core\app\Repositories;

use Illuminate\Support\Facades\Auth;
use TechStudio\Core\app\Models\Follow;
use TechStudio\Core\app\Repositories\Interfaces\FollowRepositoryInterface;

class FollowRepository implements FollowRepositoryInterface
{
    public function storeRemove($request)
    {
        $user = auth()->user();
        $followId = $request->followId;

        if ($request->do == 'follow') {
            Follow::create([
                'follower_id' => $user->id, 
                'following_id' => $followId
            ]
        );
            return response()->json([
                'status' => 'success',
                'message' => 'کاربر مورد نظر فالو شد.'
            ], 200);
        }

        if ($request->do == 'unFollow') {
            $follow = Follow::where('follower_id', $user->id)
            ->where('following_id', $followId)
            ->firstOrFail();

            $follow->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'کاربر مورد نظر آنفالو شد.'
            ], 200);
        }
    }

    public function list($request) 
    {
        // $data = Follow::where('')
    }
    
}
