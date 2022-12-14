<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => "required|email|string|exists:users,email",
            "password" => ["required"],
            "remember_me" => "boolean",
        ]);
        if (!Auth::attempt($credentials)) {
            return response(
                [
                    "error" => "The Provided credentials are not correct",
                ],
                422
            );
        }
        $user = Auth::user();
        $token = $user->createToken("main")->plainTextToken;

        return response([
            "user" => $user,
            "token" => $token,
        ]);
    }
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response(
            [
                "message" => "Logged out",
            ],
            Response::HTTP_OK
        );
    }
    public function logoutAllDevice(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response(
            [
                "status" => "success",
                "message" => "Logged out from all devices",
            ],
            Response::HTTP_OK
        );
    }
    public function register(Request $request)
    {
        $validateData = $request->validate([
            "name" => "required|string",
            "email" => "required|string|email|unique:users",
            "password" => "required|string|confirmed",
        ]);

        // $user = User::create([
        //     'name' => $validateData['name'],
        //     'email' => $validateData['email'],
        //     'user_id' => Carbon::now()->timestamp,
        //     'password' => bcrypt($validateData['password'])
        // ]);

        $user = new User();
        $user->name = $validateData["name"];
        $user->email = $validateData["email"];
        $user->user_id = Carbon::now()->timestamp;
        $user->password = bcrypt($validateData["password"]);
        $user->save();

        $token = $user->createToken("main")->plainTextToken;

        return response()->json(
            [
                "token" => $token,
                "user" => $user,
            ],
            Response::HTTP_OK
        );
    }

    public function forgotPassword(Request $request)
    {
        $validateData = $request->validate([
            "email" => "required|string|email|exists:users,email",
        ]);
        $token = '$' . bin2hex(random_bytes(16));
        $user = User::where("email", $validateData["email"])->first();
        PasswordReset::updateOrCreate(
            ["user_id" => $user->user_id],
            ["token" => $token]
        );
        Mail::send(
            "emailTemplate.resetPasswordMail",
            [
                "token" => $token,
                "username" => $user->username,
            ],
            function ($message) use ($request) {
                $message->to($request->email)->subject("Reset Password");
            }
        );
        // Session::flash("flash_message", "Send message successfully!");
        return response()->json([
            "status" => "success",
            "message" => "Send message successfully!",
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validateData = $request->validate([
            "token" => "required|string|exists:password_resets,token",
            "password" => "required|string|confirmed",
        ]);

        $user = PasswordReset::where("token", $validateData["token"])->first();
        $user = User::where("user_id", $user->user_id)->first();
        if (!$user) {
            return response(
                [
                    "status" => "error",
                    "message" => "Invalid token",
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        $user->password = bcrypt($validateData["password"]);
        $user->save();
        PasswordReset::where("token", $validateData["token"])->delete();
        return response(
            [
                "status" => "success",
                "message" => "Password reset successfully",
            ],
            Response::HTTP_OK
        );
    }

    public function user(Request $request)
    {
        $user = Auth::user();
        return response()->json($user);
    }
}
