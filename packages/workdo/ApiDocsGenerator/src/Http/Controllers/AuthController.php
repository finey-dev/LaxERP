<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\WorkSpace;
use App\Events\GivePermissionToRole;
use App\Events\DefaultData;
use Illuminate\Auth\Events\Registered;
use App\Models\EmailTemplate;
use App\Models\Plan;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {

        $validator = \Validator::make(
            $request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=>$messages->first()]);
        }

        $credentials = $request->only('email', 'password');

        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
                'status' => 'success',
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ],200);
    }

    // public function register(Request $request){
    //     $validator = \Validator::make(
    //         $request->all(), [
    //             'name' => 'required|string|max:255',
    //             'store_name' => 'required|string|max:255',
    //             'email' => 'required|string|email|max:255|unique:users',
    //             'password' => 'required|confirmed',
    //         ]
    //     );

    //     if($validator->fails())
    //     {
    //         $messages = $validator->getMessageBag();

    //         return response()->json(['status'=>'error', 'message'=>$messages->first()]);
    //     }

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);
    //     $role_r = Role::findByName('company');
    //     if(!empty($user))
    //     {
    //         $user->assignRole($role_r);
    //         // WorkSpace slug create on WorkSpace Model
    //         $workspace = new WorkSpace();
    //         $workspace->name = $request->store_name;
    //         $workspace->created_by = $user->id;
    //         $workspace->save();

    //         $user_work = User::find($user->id);
    //         $user_work->active_workspace = $workspace->id;
    //         $user_work->save();

    //         User::CompanySetting($user->id);
    //         $uArr = [
    //             'email'=> $request->email,
    //             'password'=> $request->password,
    //             'company_name'=>$request->name,
    //         ];
    //         $data= $user->MakeRole();
    //         // custom event for role
    //         $client_id =$data['client_role']->id;
    //         $staff_role =$data['staff_role']->id;
    //         if(!empty($user->active_module))
    //         {
    //             event(new GivePermissionToRole($client_id,'client',$user->active_module));
    //             event(new GivePermissionToRole($staff_role,'staff',$user->active_module));
    //             event(new DefaultData($user->id,$workspace->id,$user->active_module));
    //         }
    //         if(!empty($request->type) ? $request->type != "pricing" : '')
    //         {
    //             $plan = Plan::where('is_free_plan',1)->first();
    //             if($plan)
    //             {
    //                 $user->assignPlan($plan->id,'Month',$plan->modules,0,$user->id);
    //             }
    //         }
    //         if ( admin_setting('email_verification') == 'on')
    //         {
    //             try
    //             {
    //                 $admin_user = User::where('type','super admin')->first();
    //                 SetConfigEmail(!empty($admin_user->id) ? $admin_user->id : null);
    //                 $resp = EmailTemplate::sendEmailTemplate('New User', [$user->email], $uArr,$admin_user->id);
    //                 event(new Registered($user));
    //             }
    //             catch(\Exception $e)
    //             {
    //                 $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
    //             }
    //         }
    //         else
    //         {
    //             $user_work = User::find($user->id);
    //             $user_work->email_verified_at = date('Y-m-d h:i:s');
    //             $user_work->save();
    //         }

    //     }
    //     $token = JWTAuth::fromUser($user);
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'User created successfully',
    //         'authorisation' => [
    //             'token' => $token,
    //             'type' => 'bearer',
    //         ]
    //     ],200);
    // }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function index()
    {
        return view('api-docs-generator::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('api-docs-generator::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('api-docs-generator::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('api-docs-generator::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
