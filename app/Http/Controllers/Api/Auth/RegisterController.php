<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Traits\Api\ApiResponser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\RegisterRequest;
use App\Traits\Api\ApiServices;
use App\Traits\Auth\AuthServices;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use ApiResponser, ApiServices, AuthServices;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:api');
    }


    /**
     * Create's a user with the attempt to log in 
     *
     * @param RegisterRequest $request
     * @return Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        User::create([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (!Auth::attempt($request->only('email', 'password')))
        {
            return $this->error('Credentials mismatch', 401);
        }

        return $this->token(
            $this->getPersonalAccessToken($request),
            'Successful Registration',
            [
                'user' => Auth::user(),
                'permissions' => $this->authPermissionViaRoles()
            ]
        );
    }
}
