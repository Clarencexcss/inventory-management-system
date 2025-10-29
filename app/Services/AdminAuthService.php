<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminAuthService
{
    /**
     * Check if account is locked due to too many failed attempts
     * 
     * @param string $email
     * @return bool
     */
    public function isAccountLocked(string $email): bool
    {
        try {
            $failedAttempts = LoginAttempt::where('email', $email)
                ->where('user_type', 'admin')
                ->where('successful', false)
                ->where('attempted_at', '>=', now()->subMinutes(5))
                ->count();

            return $failedAttempts >= 3;
        } catch (\Exception $e) {
            Log::error('Failed to check account lock status', [
                'error' => $e->getMessage(),
            ]);
            return false; // Don't lock accounts if there's an error
        }
    }

    /**
     * Log failed login attempt
     * 
     * @param string $email
     * @return void
     */
    public function logFailedAttempt(string $email): void
    {
        try {
            LoginAttempt::create([
                'email' => $email,
                'ip_address' => request()->ip() ?? 'unknown',
                'user_type' => 'admin',
                'attempted_at' => now(),
                'successful' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log login attempt', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log successful login attempt
     * 
     * @param string $email
     * @return void
     */
    public function logSuccessfulAttempt(string $email): void
    {
        try {
            LoginAttempt::create([
                'email' => $email,
                'ip_address' => request()->ip() ?? 'unknown',
                'user_type' => 'admin',
                'attempted_at' => now(),
                'successful' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log login attempt', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get count of failed attempts
     * 
     * @param string $email
     * @return int
     */
    public function getFailedAttemptsCount(string $email): int
    {
        try {
            return LoginAttempt::where('email', $email)
                ->where('user_type', 'admin')
                ->where('successful', false)
                ->where('attempted_at', '>=', now()->subMinutes(5))
                ->count();
        } catch (\Exception $e) {
            Log::error('Failed to get failed attempts count', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get seconds remaining for lockout
     * 
     * @param string $email
     * @return int
     */
    public function getLockoutSecondsRemaining(string $email): int
    {
        try {
            // Get the earliest failed attempt within the last 5 minutes
            $earliestAttempt = LoginAttempt::where('email', $email)
                ->where('user_type', 'admin')
                ->where('successful', false)
                ->where('attempted_at', '>=', now()->subMinutes(5))
                ->orderBy('attempted_at', 'asc')
                ->first();

            if (!$earliestAttempt) {
                return 0;
            }

            // Calculate seconds remaining (5 minutes from the first failed attempt)
            $lockoutEnd = $earliestAttempt->attempted_at->addMinutes(5);
            $secondsRemaining = max(0, $lockoutEnd->diffInSeconds(now()));

            return $secondsRemaining;
        } catch (\Exception $e) {
            Log::error('Failed to get lockout seconds remaining', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}