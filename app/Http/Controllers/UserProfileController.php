<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use TechStudio\Blog\app\Http\Resources\AthorResource;
use TechStudio\Core\app\Http\Requests\User\RolesRequest;
use TechStudio\Core\app\Http\Requests\User\StatusRequest;
use TechStudio\Core\app\Http\Requests\User\UpdateUserRequest;
use TechStudio\Core\app\Http\Requests\User\CreateUserRequest;
use TechStudio\Core\app\Http\Resources\FollowResource;
use TechStudio\Core\app\Http\Resources\UserAddressInfoResource;
use TechStudio\Core\app\Http\Resources\UserInfoResource;
use TechStudio\Core\app\Http\Resources\UserKnsResource;
use TechStudio\Core\app\Http\Resources\UserResource;
use TechStudio\Core\app\Models\Follow;
use TechStudio\Core\app\Models\UserProfile;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class UserProfileController extends Controller
{
    public function createUser($locale, CreateUserRequest $request)
    {
        $user = UserProfile::create([
            'first_name'=> $request->firstName,
            'last_name'=>$request->lastName,
            'email'=> $request->email??null,
            'avatarUrl' => $request['avatarUrl'],
            'registration_phone_number'=> $request->phoneNumber??null,
            'password'=>Hash::make($request->password),
            'email_verified'=> Carbon::now(),
            ]);

        if (!is_null($request['role'])){
            $user->giveRolesTo($request['role']);
        }
        $role =$user->roles->map(fn($role)=>[
            "key"=> $role['key'],
            "name"=> $role['name'],
            "id"=> $role['id'],
            "permissions" => $role->permissions->map(fn($permission)=>[
                "key"=> $permission['key'],
                "name"=> $permission['name'],
                "id"=> $permission['id'],
            ])
        ]);
        $data=  [
            'id' => $user['id'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'displayName' => $user->getDisplayName(),
            'phoneNumber'=> $user['registration_phone_number'],
            'email' => $user['email'],
            'role' =>sizeof($role)?$role[0]:null,
            'avatarUrl' => $user->avatarUrl,
            'ip' => $user->ip,
            'status' => $user->status,
        ];
        return response()->json($data, 200);
    }

    public function generatePassword()
    {
        $password = random_int(10000000, 99999999);
        return response()->json([
            'password'=> $password
        ], 200);
    }

    public function getUsersListData($locale, Request $request)
    {
        $users = UserProfile::where('status','active')->with('roles');
        if ($request->filled('role')) {
            $txt = $request->get('role');
            $users = $users->whereHas('roles', function ($query) use($txt){
                $query->where('key', $txt);
            });
        }
        if ($request->filled('search')) {
            $txt = $request->get('search');
            $users = $users->where('first_name', 'like', '%' . $txt . '%')
                ->orWhere('last_name', 'LIKE', '%' . $txt . '%');
        }
        $users = $users ->latest()->paginate(10);
        $data = $users->map(fn ($user) => [
            'id' => $user->id,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'displayName' => $user->getDisplayName(),
            'phoneNumber'=> $user->registration_phone_number,
            'email' => $user->email,
            /*'roles' => [
                "name"=> $user->roles?$user->roles[0]['key']:null,
                "displayName"=> $user->roles[0]?$user->roles[0]['name']:null,
                "id"=> $user->roles[0]?$user->roles[0]['id']:null,
                ],*/
            'roles' => sizeof($user->roles)>0 ?[
                'key' => $user->roles[0]['key'],
                'name' => $user->roles[0]['name'],
                'id' =>$user->roles[0]['id'],
            ]:null,

            'avatarUrl' => $user->avatarUrl,
            'ip' => $user->ip,
            'status' => $user->status
        ]);

        return [
            'total' => $users->total(),
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'last_page' => $users->lastPage(),
            'data' => $data
        ];
    }

    public function getUsersListCommon()
    {
        $activeUser= UserProfile::where('status','active')->count();
        $roles = Role::all()->map(fn ($role) => [
            'key' => $role->key,
            'name' => $role->name,
            'id' => $role->id,
        ]);
         return response()->json([
             'counts' => [ 'totalUsers' => $activeUser,],
             'roles' => $roles
             ], 200);
    }

    public function createUserCommon()
    {
        $roles = Role::all()->map(fn ($role) => [
            'key' => $role->key,
            'name' => $role->name,
            'id' => $role->id,
        ]);
        return response()->json([
            'roles' => $roles,
        ], 200);
    }

    public function setRoles($locale, RolesRequest $request)
    {
        foreach ($request['userIds'] as $userId) {
            $user = UserProfile::find($userId);

            if ($user) {
              foreach ($request['roles'] as $role){
                  $user->refreshRoles($role);
                }
            }
        }
        return response()->json([
            'userIds'=> $request['userIds']
        ], 200);
    }

    public function setStatus($locale, StatusRequest $request)
    {
        UserProfile::whereIn('user_id', $request['ids'])
            ->update(['status'=>$request['status']]);

        User::whereIn('id', $request['ids'])
            ->update(['status'=>$request['status']]);

        return response()->json([
            'userIds'=> $request['ids'],
            'status' => $request['status']
        ], 200);
    }

    public function editUser($locale, UserProfile $user)
    {
        //$user->load('roles');
        $role =$user->roles->map(fn($role)=>[
            "key"=> $role['key'],
            "name"=> $role['name'],
            "id"=> $role['id'],
            "permissions" => $role->permissions->map(fn($permission)=>[
                "key"=> $permission['key'],
                "name"=> $permission['name'],
                "id"=> $permission['id'],
            ])
        ]);

        $data = $user->toArray();

        return [
            'id' => $data['id'],
            'firstName' => $data['first_name'],
            'lastName' => $data['last_name'],
            'displayName' => $user->getDisplayName(),
            'phoneNumber'=> $data['registration_phone_number'],
            'email' => $data['email'],
            'role' =>sizeof($role)?$role[0]:null,
            'avatarUrl' => $user->avatarUrl,
            'ip' => $user->ip,
            'status' => $user->status,
        ];
    }

    public function updateUser($locale, UserProfile $user,UpdateUserRequest $request)
    {
        $userUnique =  UserProfile::where(function ($q) use($request){
                $q->orWhere('registration_phone_number',$request['phoneNumber'])
                    ->orWhere('email','like',"%".$request['email']."%");
            })->where('id','!=',96)->first();

        if ($request->filled('phoneNumber')){
            if ($userUnique){
                return response()->json([
                    'message' => __('Error PhoneNumber/Email'),
                ], 422);
            }
        }
        $user->update([
            'first_name' => $request['firstName'],
            'last_name' => $request['lastName'],
            'registration_phone_number'=> $request['phoneNumber'],
            'email' => $request['email'],
            'avatarUrl' => $request['avatarUrl'],
            'status' => $request['status'],
        ]);

        if (is_null($request['role'])){
            $user->withdrawRoles($user->roles->pluck('id'));
        }else{
            $user->refreshRoles($request->role);
            $user->load('roles');
            $role = collect($user->roles)->map(fn($role)=>[
                "name"=> $role['key'],
                "displayName"=> $role['name'],
                "id"=> $role['id'],
            ])->collect();
        }

        $data = $user->toArray();
        return [
            'id' => $data['id'],
            'firstName' => $data['first_name'],
            'lastName' => $data['last_name'],
            'displayName' => $user->getDisplayName(),
            'registration_phone_number'=> $data['registration_phone_number'],
            'email' => $data['email'],
            'roles' =>$role??null,
            'avatarUrl' => $user->avatarUrl,
            'ip' => $user->ip,
            'status' => $user->status
        ];
    }

    public function getUserData()
    {
        $user = Auth::user();

        return [
            'personalInfo' => new UserInfoResource($user),
            'addressInfo' => new UserAddressInfoResource($user),
        ];
    }

    public function editData(Request $request)
    {
        $userId = auth()->user()->id;

        $keyRequest = [];
        $keyMain = [];

        if ($request['firstName']) {
            $keyRequest ['first_name'] = $request['firstName'];
            $keyMain ['first_name'] = $request['firstName'];
        }
        if ($request['lastName']) {
            $keyRequest ['last_name'] = $request['lastName'];
            $keyMain ['last_name'] = $request['lastName'];
        }
        if ($request['phone']) {
            $keyRequest ['registration_phone_number'] = $request['phone'];
        }
        if ($request['avatarUrl']) {
            $keyRequest ['avatar_url'] = $request['avatarUrl'];
            $keyMain ['avatar_url'] = $request['avatarUrl'];
        }
        if ($request['shopLink']) {
            $keyRequest ['shop_website'] = $request['shopLink'];
        }
        if ($request['postalCode']) {
            $keyRequest ['postal_code'] = $request['postalCode'];
        }
        if ($request['birthday']) {
            $keyRequest ['birthday'] = $request['birthday'];
            $keyMain ['birthday'] = $request['birthday'];
        }
        if ($request['job']) {
            $keyRequest ['job'] = $request['job'];
            $keyMain ['job'] = $request['job'];
        }
        if ($request['email']) {
            $keyRequest ['email'] = $request['email'];
        }

        $mainUser = new User();
        if ($mainUser){
           /*if ($mainUser->where('username', $request['phone'])->first()){
               //age seller bod natone taghir bde

               //age seller nabod natone taghir bde
               return response()->json([
                   'message' => 'این شماره موبایل قبلا ثبت شده است.',
               ], 400);
           }*/
            /*$sss = $mainUser->where('id', $userId)->update([
                'first_name' => $request['firstName'],
                'last_name' => $request['lastName'],
                'birthday' => $request['birthday'],
                'job' => $request['job'],
                'username' => Auth::user()->username,
                'avatar_url' => $request['avatarUrl'],

            ]);*/
             $mainUser->where('id', $userId)->update(array_merge($request->only(
                 'first_name',
                 'last_name',
                 'birthday',
                 'job',
                 'avatar_url'
             ),$keyMain));
        }
        $user = UserProfile::where('user_id', $userId)->update(array_merge($request->only(
            'description',
            'email',
            'birthday',
            'job',
            'state',
            'city',
            'street',
            'block',
            'unit',
            'postal_code'
        ),
            $keyRequest
        ));

        if (class_exists(Profile::class)) {
            Profile::where('user_id', $userId)->update(
                [
                    'first_name' => $request['firstName'],
                    'last_name' => $request['lastName'],
                    'avatar_url' => $request['avatarUrl'],
                    'job_title' => $request['job'],
                    'email' => $request['email'],
                    'description' => $request['description']
                ]
            );
        }

        $user = UserProfile::where('user_id', $userId)->firstOrFail();

        return $user;
    }

    public function knsUserData(Request $request)
    {
        $user = UserProfile::with(['following', 'follower'])->where('user_id', $request->userId)->firstOrFail();

        $following = UserProfile::whereHas('following', function($query) use($request) {
            $query->where('follower_id','=', $request->userId);
        })->orderby('id', 'DESC')->get();
            
        return [
            'data' => [
                'info' => new UserResource($user),
                'following' => FollowResource::collection($following)
            ]
        ];
    }
}
