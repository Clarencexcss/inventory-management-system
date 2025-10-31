<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        $failedAttempts = LoginAttempt::where('email', $email)
            ->where('user_type', 'admin')
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subMinutes(5))
            ->count();

        return $failedAttempts >= 3;
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
            Log::error('Failed to log admin login attempt', [
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
            Log::error('Failed to log admin login attempt', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Clear failed login attempts after successful login
     * 
     * @param string $email
     * @return void
     */
    public function clearFailedAttempts(string $email): void
    {
        try {
            // Delete failed attempts older than 5 minutes
            LoginAttempt::where('email', $email)
                ->where('user_type', 'admin')
                ->where('successful', false)
                ->where('attempted_at', '<', now()->subMinutes(5))
                ->delete();
        } catch (\Exception $e) {
            Log::error('Failed to clear admin failed login attempts', [
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
            Log::error('Failed to get admin failed attempts count', [
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
            Log::error('Failed to get admin lockout seconds remaining', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
    
    /**
     * Get the appropriate warning message based on failed attempts
     * 
     * @param string $email
     * @param int $totalFailedAttempts Total failed attempts including the current one
     * @return array
     */
    public function getWarningMessage(string $email, int $totalFailedAttempts = null): array
    {
        // If totalFailedAttempts is not provided, get it from the database
        if ($totalFailedAttempts === null) {
            $totalFailedAttempts = $this->getFailedAttemptsCount($email);
        }
        
        // The messages are based on the total number of failed attempts
        switch ($totalFailedAttempts) {
            case 1:
                return [
                    'message' => '⚠️ You have 2 attempts remaining before your account is locked for 5 minutes.',
                    'type' => 'warning',
                    'remaining_attempts' => 2
                ];
            case 2:
                return [
                    'message' => '⚠️ You have 1 attempt remaining before your account is locked for 5 minutes.',
                    'type' => 'warning',
                    'remaining_attempts' => 1
                ];
            case 3:
                return [
                    'message' => '⚠️ This is your last attempt. If you fail again, your account will be locked for 5 minutes.',
                    'type' => 'warning',
                    'remaining_attempts' => 0
                ];
            default:
                return [
                    'message' => '',
                    'type' => '',
                    'remaining_attempts' => max(0, 3 - $totalFailedAttempts)
                ];
        }
    }
}