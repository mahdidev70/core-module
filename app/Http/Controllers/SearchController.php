<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TechStudio\Core\app\Models\UserProfile;

class SearchController extends Controller
{
    public function searchUser(Request $request)
    {
        $txt = $request->query->get('query');
        $res = [];
       if ($txt){
           $users = UserProfile::where('status','active')->where(function($q) use($txt){
               $q->where('first_name','like', '%'.$txt)->orWhere('first_name', 'like', '% '.$txt.'%')->orWhere('first_name','like',$txt.'%')
               ->orWhere('last_name','like', '%'.$txt)->orWhere('last_name', 'like', '% '.$txt.'%')->orWhere('last_name','like',$txt.'%');
           })->take(10)->get(['first_name','last_name','id','avatar_url']);

           $res = $users->map(fn($user) => [
               'userId' => $user->id,
               'userDisplayName' => $user->getDisplayName(),
               'avatarUrl' => $user->avatar_url,
           ]);
       }

        return response()->json($res);
     }
}
