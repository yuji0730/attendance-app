<?php

namespace App\Providers;

use App\Models\User;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Requests\LoginRequest;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });


        Fortify::loginView(function () {
            // 管理者ログイン画面を出したいとき
            if (request()->is('admin/login')) {
                return view('admin.auth.login');
            }

            // 通常のユーザー
            return view('auth.login');
        });

        // Fortify::authenticateUsing(function ($request) {
        //     $formRequest = app(LoginRequest::class);
        //     $formRequest->setContainer(app())->setRedirector(app('redirect'))->merge($request->all());

        //     $validator = \Validator::make(
        //         $formRequest->all(),
        //         $formRequest->rules(),
        //         [
        //             'email.required' => 'メールアドレスを入力してください',
        //             'password.required' => 'パスワードを入力してください',
        //         ]
        //     );

        //     if ($validator->fails()) {
        //         // カスタムメッセージを配列で明示的に指定して ValidationException を投げる
        //         throw ValidationException::withMessages([
        //             'email' => $validator->errors()->get('email') ?: ['メールアドレスを入力してください'],
        //             'password' => $validator->errors()->get('password') ?: ['パスワードを入力してください'],
        //         ]);
        //     }

        //     $user = User::where('email', $formRequest->email)->first();

        //     if (!$user || !Hash::check($formRequest->password, $user->password)) {
        //         throw ValidationException::withMessages([
        //             'password' => ['ログイン情報が登録されていません'],
        //         ]);
        //     }

        //     return $user;
        // });


        // Fortify::authenticateUsing(function ($request) {
        //     // FormRequest でルールを取得
        //     $formRequest = app(LoginRequest::class);
        //     $formRequest->setContainer(app())->setRedirector(app('redirect'))->merge($request->all());

        //     $validator = \Validator::make(
        //         $formRequest->all(),
        //         $formRequest->rules(),
        //         [
        //             'email.required' => 'メールアドレスを入力してください',
        //             'password.required' => 'パスワードを入力してください',
        //         ]
        //     );

        //     if ($validator->fails()) {
        //         throw new ValidationException($validator);
        //     }

        //     // 認証処理
        //     $user = User::where('email', $formRequest->email)->first();

        //     if (!$user || !Hash::check($formRequest->password, $user->password)) {
        //         throw ValidationException::withMessages([
        //             'password' => ['ログイン情報が登録されていません'],
        //         ]);
        //     }

        //     return $user;
        // });

        // Fortify::authenticateUsing(function ($request) {
        //     $formRequest = app(LoginRequest::class);
        //     $formRequest->setContainer(app())->setJson($request->json())->merge($request->all());
        //     $formRequest->validateResolved();

        //     $user = \App\Models\User::where('email', $formRequest->email)->first();

        //     if (!$user || !\Hash::check($formRequest->password, $user->password)) {
        //         throw ValidationException::withMessages([
        //             'email' => ['認証情報が正しくありません'],
        //         ]);
        //     }

        //     return $user;
        // });

        // Fortify::authenticateUsing(function (LoginRequest $request) {
        //     // FormRequestのバリデーション実行
        //     $request->validate($request->rules(), $request->messages());

        //     $user = \App\Models\User::where('email', $request->email)->first();

        //     if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        //         throw \Illuminate\Validation\ValidationException::withMessages([
        //             'email' => ['ログイン情報が登録されていません'],
        //         ]);
        //     }

        //     return $user;
        // });

        // Fortify::authenticateThrough(function ($request) {
        //     return [
        //         \Laravel\Fortify\Actions\AttemptToAuthenticate::class,
        //         function ($request) {
        //             $user = Auth::user();
        //             return redirect($user->is_admin ? '/admin/index' : '/attendance');
        //         },
        //     ];
        // });

        // Fortify::authenticated(function ($request) {
        //     $user = Auth::user();
        //     return redirect($user->is_admin ? '/admin/index' : '/attendance');
        // });
    }
}
