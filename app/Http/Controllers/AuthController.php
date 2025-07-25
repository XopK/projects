<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;

use App\Models\PhoneVerification;
use App\Models\User;
use App\Notifications\ResetPassword;
use App\Services\PhoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    protected $phoneService;

    public function __construct(PhoneService $phoneService)
    {
        $this->phoneService = $phoneService;
    }

    public function signUp(RegistrationRequest $request)
    {
        try {
            $code = rand(1000, 9999);
            $token = Str::uuid();

            PhoneVerification::where('phone', $request->phone)->delete();

            PhoneVerification::create([
                'session_token' => $token,
                'phone' => $request->phone,
                'code' => $code,
                'data' => json_encode([
                    'name' => $request->name,
                    'nickname' => $request->nickname,
                    'email' => $request->email,
                    'birthday' => $request->birthday,
                    'photo_profile' => 'https://ui-avatars.com/api/?background=09090b&color=fff&name=' . urlencode($request->name) . '&size=128&length=1',
                    'password' => Hash::make($request->password),
                ]),
                'expires_at' => now()->addMinutes(15),
            ]);

            $number = preg_replace('/\D+/', '', $request->phone);
            $message = 'Ваш код для регистрации на сайте vsetancy.ru: ' . $code;

            $this->phoneService->sendSMS($number, $message);

            return redirect()->route('confirmPhone', ['token' => $token]);

        } catch (\Exception $exception) {

            Log::channel('errorlog')->error('Ошибка регистрации пользователя: ' . $exception->getMessage());

            return abort(500);

        }
    }

    public function signIn(LoginRequest $request)
    {
        try {
            $emailOrPhone = filter_var($request->signInData, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            if (Auth::attempt([

                $emailOrPhone => $request->signInData,
                'password' => $request->password,

            ], $request->has('remember'))) {

                return redirect('/')->with('success', 'Авторизация прошла успешно!');

            } else {

                return redirect()->back()->with('error', 'Проверьте введеные данные.');

            }

        } catch (\Exception $exception) {

            Log::channel('errorlog')->error('Ошибка авторизации пользователя: ' . $exception->getMessage());

            return abort(500);

        }
    }

    public function signOut()
    {
        Auth::logout();
        Session::flush();
        return redirect('/')->with('success', 'Вы вышли из аккаунта!');
    }

    public function forgotPassword()
    {
        return view('pages.forgot_password');
    }

    public function forgotPasswordSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ], [
            'email.exists' => 'Такой почты на сайте нет.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input("email");

        $user = User::where('email', $email)->first();
        $token = Password::createToken(User::where('email', $email)->first());

        $user->notify(new ResetPassword($token));

        return response()->json([
            'success' => true
        ]);
    }

    public function resetPassword(Request $request, $token)
    {
        $email = $request->get('email');
        $user = User::where('email', $email)->first();

        if (!$token || !$email || !$user) {
            abort(404);
        }

        if (!Password::tokenExists($user, $token)) {
            abort(404);
        }

        return view('pages.reset_password', ['token' => $token, 'user' => $user->id]);
    }

    public function resetPasswordUpdate(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
            'token' => 'required',
            'user' => 'required',
        ]);

        try {
            $user = User::findOrFail($request->input('user'));

            if (!Password::tokenExists($user, $request->input('token'))) {
                abort(404);
            }

            $user->update([
                'password' => Hash::make($request->input('new_password')),
            ]);

            Password::deleteToken($user);

            return redirect(route('index'))->with('success', 'Пароль успешно изменен.');

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return redirect(route('index'))->with('error', 'Ошибка изменения пароля, попробуйте ещё раз!');
        }

    }

    public function confirmPhone($token)
    {
        $verification = PhoneVerification::where('session_token', $token)->first();

        if (!$verification || $verification->expires_at < now()) {
            return redirect()->route('index')->with('error', 'Сессия истекла. Зарегистрируйтесь заново.');
        }

        return view('pages.confirm_phone', ['token' => $token]);
    }

    public function confirmPhoneVerify(Request $request, $token)
    {
        $request->validate([
            'code' => 'required|digits:4',
        ]);

        $verification = PhoneVerification::where('session_token', $token)->first();

        if (!$verification || $verification->expires_at < now()) {
            return redirect()->route('index')->with('error', 'Сессия истекла. Зарегистрируйтесь заново.');
        }

        if ($request->code !== $verification->code) {
            return redirect()->back()->with('error', 'Неверный код.');
        }

        $userData = json_decode($verification->data, true);
        $userData['phone'] = $verification->phone;

        $user = User::create($userData);

        $verification->delete();
        Auth::login($user);

        return redirect()->route('index')->with('success', 'Успешная регистрация!');
    }
}
