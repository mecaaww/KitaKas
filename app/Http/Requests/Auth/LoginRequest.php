<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gender'   => ['required', 'string', 'in:male,female'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * FIX: Login manual berdasarkan gender + password.
     * gender tidak unik secara teknis tapi dalam sistem ini
     * hanya ada 1 user male dan 1 user female.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('gender', $this->input('gender'))->first();

        if (! $user || ! Hash::check($this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'gender' => __('auth.failed'),
            ]);
        }

        // Login user secara manual
        Auth::login($user, false);

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'gender' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->input('gender')) . '|' . $this->ip();
    }
}
