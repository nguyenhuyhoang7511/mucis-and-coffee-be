<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // $remember = $request->has('remember');

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

        return response()->json(['message' => 'Invalid login credentials'], 401);
    }
    public function activeAcount(Request $request)
    {
        try {
            $userActive = User::where('email', $request->email)->first();

            if($userActive->number_code == $request->number_code)
            {
                $userActive->update([
                    'is_active' => true
                ]);
                return response()->json(['message' => 'kích hoạt thành công'], 201);
            }
        return response()->json(['message' => 'Mã kích hoạt không đúng'], 500);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Kích hoạt thất bại'], 500);
        }
    }
}
