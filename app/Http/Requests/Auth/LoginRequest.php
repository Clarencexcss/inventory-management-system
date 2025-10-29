<?php

namespace App\Http\Requests\Auth;

use App\Services\AdminAuthService;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    protected $adminAuthService;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->adminAuthService = app(AdminAuthService::class);
    }

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
            'email' => ['required', 'string', 'email', 'exists:users,email'],
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

        // Check if the account is locked due to too many failed attempts
        if ($this->adminAuthService->isAccountLocked($this->email)) {
            // Calculate seconds remaining for lockout
            $secondsRemaining = $this->adminAuthService->getLockoutSecondsRemaining($this->email);
            $message = 'Account temporarily locked due to multiple failed login attempts. Please try again in ' . $secondsRemaining . ' seconds.';
            
            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            $this->adminAuthService->logFailedAttempt($this->email);
            
            // Get current failed attempts count (before this attempt)
            $failedAttempts = $this->adminAuthService->getFailedAttemptsCount($this->email);
            $totalAttemptsAfterThis = $failedAttempts + 1;
            
            // Check if this attempt will cause a lockout
            if ($totalAttemptsAfterThis >= 3) {
                // This is the third failed attempt, account will be locked
                $message = 'Invalid credentials. This was your third failed attempt. Your account is now locked for 5 minutes.';
            } else {
                // Still have attempts remaining
                $remainingAttempts = 3 - $totalAttemptsAfterThis;
                $message = trans('auth.failed');
                $message .= ' You have ' . $remainingAttempts . ' attempt(s) remaining before your account is locked for 5 minutes.';
            }

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        $this->adminAuthService->logSuccessfulAttempt($this->email);
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
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')) . '|' . $this->ip();
    }
}