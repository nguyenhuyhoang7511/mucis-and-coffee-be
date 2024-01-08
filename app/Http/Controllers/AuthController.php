<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Jobs\SendEmail;
use App\Models\User;
use Exception;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterFormRequest $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'userName' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            $user = User::create([
                'name' => $request->userName,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $numberCode = mt_rand(100000, 999999);

            $user->update([
                'number_code' => $numberCode
            ]);

            SendEmail::dispatch($numberCode, $user)->delay(now()->addMinute(1));

            DB::commit();

            return response()->json(['message' => 'Registration successful'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registration failed'], 500);
        }
    }

    public function login(LoginFormRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password ])) {
            $user = Auth::user();
            if ($user->is_active == 0) {
                Auth::logout(); 
                return response()->json(['message' => 'Tài khoản chưa được kích hoạt.'], 401);
            }
            if ($user->is_active == 2) {
                Auth::logout(); 
                return response()->json(['message' => 'Tài khoản đã bị khoá, vui lòng liên hệ quản trị viên.'], 403);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
        }

        return response()->json(['message' => 'Tài khoản hoặc mật khẩu không chính xác'], 401);
    }
    public function activeAcount(Request $request)
    {
        try {
            $userActive = User::where('email', $request->email)->first();

            if($userActive->number_code == $request->number_code)
            {
                $userActive->update([
                    'is_active' => true,
                    'number_code' => null
                ]);
                return response()->json(['message' => 'kích hoạt thành công'], 201);
            }
            if(!$userActive->number_code){
                return response()->json(['message' => 'không thể kích hoạt một tài khoản đã được kích hoạt'], 500);
            }
            return response()->json(['message' => 'Mã kích hoạt không đúng'], 500);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Kích hoạt thất bại'], 500);
        }
    }

    public function redirectToGoogle() 
    {
        return response()->json(['url' => Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl()]);
    }

    public function handleGoogleCallback()
    {   
        try {
            $user = Socialite::driver('google')->stateless()->user();

            $finduser = User::where('email', $user->email)->orWhere(function ($q) use ($user) {
                $q->where('provider_name', 'google')->where('google_id', $user->id);
            })->first();
 
            if ($finduser == null) {
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'password' => bcrypt(Str::random(10)),
                    'provider_name' => 'google',
                    'google_id' => $user->id,
                    'avatar' => $user->getAvatar(),
                    'is_active' => 1 
                ]);

                $token = $newUser->createToken('auth_token')->plainTextToken;
                return response()->json(['token' => $token,'token_type' => 'Bearer']);
            }

            $token = $finduser->createToken('auth_token')->plainTextToken;
            return response()->json(['token' => $token,'token_type' => 'Bearer']);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
