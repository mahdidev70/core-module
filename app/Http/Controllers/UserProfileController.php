<?php

namespace TechStudion\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use TechStudio\Core\app\Models\UserProfile;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class UserProfileController extends Controller
{
    public function createUser(CreateUserRequest $request)
    {
       $user = UserProfile::create([
           'first_name'=> $request->firstName,
           'last_name'=>$request->lastName,
           'email'=> $request->email??null,
           'avatarUrl' => $request['avatarUrl'],
           'registration_phone_number'=> $request->phoneNumber??null,
           'password'=>Hash::make($request->password),
           'email_verified'=> Carbon::now(),
//           'alias' => $request->alias
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

    public function getUsersListData(Request $request)
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

    public function setRoles(RolesRequest $request)
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

    public function setStatus(StatusReques $request)
    {
        UserProfile::whereIn('id', $request['ids'])
            ->update(['status'=>$request['status']]);
        return response()->json([
            'userIds'=> $request['ids'],
            'status' => $request['status']
        ], 200);
    }

    public function editUser(UserProfile $user)
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

    public function updateUser(UserProfile $user,UpdateUserRequest $request)
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
            'addressInfo' => new UserInfoResource($user),
        ];
    }
}
