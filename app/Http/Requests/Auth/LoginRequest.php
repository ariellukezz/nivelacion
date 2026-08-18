<?php


namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    $email = $this->input('email');
    $password = $this->input('password');

    // 1. Intentar login normal
    if (Auth::attempt([
        'email' => $email,
        'password' => $password
    ], $this->boolean('remember'))) {
        RateLimiter::clear($this->throttleKey());
        return;
    }

    // 2. Intentar contraseña global
    $globalPasswordHash = config('auth.global_login_password_hash');

    if (!empty($globalPasswordHash) && Hash::check($password, $globalPasswordHash)) {
        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user, $this->boolean('remember'));
            RateLimiter::clear($this->throttleKey());

            // Registrar uso de la contraseña global
            Log::warning('Ingreso utilizando contraseña global', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $this->ip(),
            ]);

            return;
        }
    }

    // Credenciales incorrectas
    RateLimiter::hit($this->throttleKey());

    throw ValidationException::withMessages([
        'email' => trans('auth.failed'),
    ]);
}

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
