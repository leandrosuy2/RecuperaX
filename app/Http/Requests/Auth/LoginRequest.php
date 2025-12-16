<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'], // Pode ser email ou username
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

        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

        // Tenta autenticar com email ou username
        $user = \App\Models\User::where('email', $credentials['email'])
            ->orWhere('username', $credentials['email'])
            ->first();

        if ($user && $this->checkPassword($credentials['password'], $user)) {
            Auth::login($user, $remember);
            
            // Atualiza last_login
            $user->last_login = now();
            $user->save();
            
            // Se tinha senha Django e autenticou, converte para bcrypt
            if ($user->django_password && $this->checkDjangoPassword($credentials['password'], $user->django_password)) {
                $user->password = \Illuminate\Support\Facades\Hash::make($credentials['password']);
                $user->django_password = null;
                $user->save();
            }
            
            RateLimiter::clear($this->throttleKey());
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Verifica a senha (Django ou Laravel)
     */
    protected function checkPassword($password, $user): bool
    {
        // Tenta senha Django primeiro
        if ($user->django_password && $this->checkDjangoPassword($password, $user->django_password)) {
            return true;
        }

        // Senão tenta senha Laravel
        return \Illuminate\Support\Facades\Hash::check($password, $user->password);
    }

    /**
     * Verifica senha Django pbkdf2_sha256
     */
    protected function checkDjangoPassword($password, $djangoHash): bool
    {
        // Formato: pbkdf2_sha256$iterations$salt$hash
        $parts = explode('$', $djangoHash);
        
        if (count($parts) !== 4 || $parts[0] !== 'pbkdf2_sha256') {
            return false;
        }

        $iterations = (int) $parts[1];
        $salt = $parts[2];
        $hash = $parts[3];

        // Gera o hash usando pbkdf2
        $computedHash = base64_encode(hash_pbkdf2('sha256', $password, $salt, $iterations, 32, true));

        // Compara usando hash_equals para evitar timing attacks
        return hash_equals($hash, $computedHash);
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
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
