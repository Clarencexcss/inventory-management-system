<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAuthLog;
use App\Models\LoginAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class CustomerAuthService
{
    /**
     * Create a new customer account
     * 
     * @param array $data
     * @return array
     */
    public function createCustomerAccount(array $data): array
    {
        try {
            DB::beginTransaction();

            // Create customer
            $customer = Customer::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'address' => $data['address'],
                'status' => 'active',
                'role' => 'customer',
            ]);

            // Log the account creation
            CustomerAuthLog::create([
                'customer_id' => $customer->id,
                'action' => 'account_created',
                'ip_address' => request()->ip() ?? 'unknown',
                'user_agent' => request()->userAgent() ?? 'unknown',
                'details' => [
                    'email' => $customer->email,
                    'username' => $customer->username,
                    'created_at' => now()->toDateTimeString(),
                ],
            ]);

            // Create token
            $token = $customer->createToken('customer-token')->plainTextToken;

            DB::commit();

            Log::info('Customer account created successfully', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'username' => $customer->username,
            ]);

            return [
                'success' => true,
                'message' => 'Customer account created successfully',
                'customer' => $customer,
                'token' => $token,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create customer account', [
                'error' => $e->getMessage(),
                'data' => Arr::except($data, ['password', 'password_confirmation']),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create customer account: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Login customer
     * 
     * @param string $emailOrUsername
     * @param string $password
     * @return array
     */
    public function loginCustomer(string $emailOrUsername, string $password): array
    {
        try {
            // Check if the account is locked due to too many failed attempts
            if ($this->isAccountLocked($emailOrUsername, 'customer')) {
                // Calculate seconds remaining for lockout
                $secondsRemaining = $this->getLockoutSecondsRemaining($emailOrUsername, 'customer');
                $minutesRemaining = ceil($secondsRemaining / 60);
                $message = 'Account temporarily locked due to multiple failed login attempts. Please try again in ' . $minutesRemaining . ' minutes.';
                
                return [
                    'success' => false,
                    'message' => $message,
                    'type' => 'locked',
                    'lockout_seconds' => $secondsRemaining
                ];
            }

            DB::beginTransaction();

            // Find customer by email or username
            $customer = Customer::where('email', $emailOrUsername)
                ->orWhere('username', $emailOrUsername)
                ->first();

            if (!$customer) {
                $this->logFailedLogin($emailOrUsername, 'Customer not found');
                $this->logFailedAttempt($emailOrUsername, 'customer');
                return [
                    'success' => false,
                    'message' => 'Invalid email or username. Please check your credentials and try again.',
                    'type' => 'invalid_credentials'
                ];
            }

            // Check password
            if (!Hash::check($password, $customer->password)) {
                // Log the current failed attempt FIRST
                $this->logFailedLogin($emailOrUsername, 'Invalid password', $customer->id);
                $this->logFailedAttempt($emailOrUsername, 'customer');
                
                // NOW check if this attempt caused a lockout
                // Get current failed attempts count (AFTER this attempt is logged)
                $failedAttempts = $this->getFailedAttemptsCount($emailOrUsername, 'customer');
                
                // Check if account should now be locked (3 or more failed attempts)
                if ($failedAttempts >= 3) {
                    // Account is now locked
                    $message = 'Account temporarily locked due to multiple failed login attempts. Please try again in 5 minutes.';
                    return [
                        'success' => false,
                        'message' => $message,
                        'type' => 'locked_after_third_attempt',
                        'lockout_seconds' => 300 // 5 minutes in seconds
                    ];
                } else {
                    // Still have attempts remaining
                    // Calculate remaining attempts: 3 total - current failed attempts
                    $remainingAttempts = 3 - $failedAttempts;
                    $message = 'Invalid password. Please check your password and try again.';
                    $message .= ' You have ' . $remainingAttempts . ' attempt(s) remaining before your account is locked for 5 minutes.';
                    
                    return [
                        'success' => false,
                        'message' => $message,
                        'type' => 'invalid_password',
                        'remaining_attempts' => $remainingAttempts
                    ];
                }
            }

            // Check if account is active
            if (!$customer->isActive()) {
                $this->logFailedLogin($emailOrUsername, 'Account suspended', $customer->id);
                $this->logFailedAttempt($emailOrUsername, 'customer');
                return [
                    'success' => false,
                    'message' => 'Your account has been suspended. Please contact support.',
                    'type' => 'suspended'
                ];
            }

            // Log successful attempt
            $this->logSuccessfulAttempt($emailOrUsername, 'customer');

            // Create token
            $token = $customer->createToken('customer-token')->plainTextToken;

            // Log successful login
            CustomerAuthLog::create([
                'customer_id' => $customer->id,
                'action' => 'login_success',
                'ip_address' => request()->ip() ?? 'unknown',
                'user_agent' => request()->userAgent() ?? 'unknown',
                'details' => [
                    'login_method' => 'email_or_username',
                    'login_identifier' => $emailOrUsername,
                    'login_time' => now()->toDateTimeString(),
                ],
            ]);

            // Update last login time
            $customer->update([
                'last_login_at' => now(),
            ]);

            DB::commit();

            Log::info('Customer logged in successfully', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'username' => $customer->username,
                'ip_address' => request()->ip() ?? 'unknown',
            ]);

            return [
                'success' => true,
                'message' => 'Login successful. Welcome back!',
                'customer' => $customer,
                'token' => $token,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to login customer', [
                'error' => $e->getMessage(),
                'email_orUsername' => $emailOrUsername,
            ]);

            return [
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage(),
                'type' => 'error'
            ];
        }
    }

    /**
     * Logout customer
     * 
     * @param Customer $customer
     * @return array
     */
    public function logoutCustomer(Customer $customer): array
    {
        try {
            DB::beginTransaction();

            // Get current token
            $currentToken = $customer->currentAccessToken();

            // Log the logout
            CustomerAuthLog::create([
                'customer_id' => $customer->id,
                'action' => 'logout',
                'ip_address' => request()->ip() ?? 'unknown',
                'user_agent' => request()->userAgent() ?? 'unknown',
                'details' => [
                    'logout_time' => now()->toDateTimeString(),
                    'token_id' => $currentToken ? $currentToken->id : null,
                ],
            ]);

            // Delete current token
            if ($currentToken) {
                $currentToken->delete();
            }

            DB::commit();

            Log::info('Customer logged out successfully', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'username' => $customer->username,
                'ip_address' => request()->ip() ?? 'unknown',
            ]);

            return [
                'success' => true,
                'message' => 'Logged out successfully',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to logout customer', [
                'error' => $e->getMessage(),
                'customer_id' => $customer->id,
            ]);

            return [
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Log failed login attempts
     * 
     * @param string $emailOrUsername
     * @param string $reason
     * @param int|null $customerId
     * @return void
     */
    private function logFailedLogin(string $emailOrUsername, string $reason, ?int $customerId = null): void
    {
        try {
            CustomerAuthLog::create([
                'customer_id' => $customerId,
                'action' => 'login_failed',
                'ip_address' => request()->ip() ?? 'unknown',
                'user_agent' => request()->userAgent() ?? 'unknown',
                'details' => [
                    'login_identifier' => $emailOrUsername,
                    'failure_reason' => $reason,
                    'attempt_time' => now()->toDateTimeString(),
                ],
            ]);

            Log::warning('Failed login attempt', [
                'email_or_username' => $emailOrUsername,
                'reason' => $reason,
                'ip_address' => request()->ip() ?? 'unknown',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log failed login attempt', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log failed login attempt
     * 
     * @param string $emailOrUsername
     * @param string $userType
     * @return void
     */
    private function logFailedAttempt(string $emailOrUsername, string $userType): void
    {
        try {
            LoginAttempt::create([
                'email' => $emailOrUsername,
                'ip_address' => request()->ip() ?? 'unknown',
                'user_type' => $userType,
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
     * @param string $emailOrUsername
     * @param string $userType
     * @return void
     */
    private function logSuccessfulAttempt(string $emailOrUsername, string $userType): void
    {
        try {
            LoginAttempt::create([
                'email' => $emailOrUsername,
                'ip_address' => request()->ip() ?? 'unknown',
                'user_type' => $userType,
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
     * Check if account is locked due to too many failed attempts
     * 
     * @param string $emailOrUsername
     * @param string $userType
     * @return bool
     */
    private function isAccountLocked(string $emailOrUsername, string $userType): bool
    {
        try {
            $failedAttempts = LoginAttempt::where('email', $emailOrUsername)
                ->where('user_type', $userType)
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
     * Get count of failed attempts
     * 
     * @param string $emailOrUsername
     * @param string $userType
     * @return int
     */
    private function getFailedAttemptsCount(string $emailOrUsername, string $userType): int
    {
        try {
            return LoginAttempt::where('email', $emailOrUsername)
                ->where('user_type', $userType)
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
     * @param string $emailOrUsername
     * @param string $userType
     * @return int
     */
    private function getLockoutSecondsRemaining(string $emailOrUsername, string $userType): int
    {
        try {
            // Get the earliest failed attempt within the last 5 minutes
            $earliestAttempt = LoginAttempt::where('email', $emailOrUsername)
                ->where('user_type', $userType)
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

    /**
     * Get customer authentication history
     * 
     * @param Customer $customer
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuthHistory(Customer $customer, int $limit = 50)
    {
        return CustomerAuthLog::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get failed login attempts for an IP address
     * 
     * @param string $ipAddress
     * @param int $minutes
     * @return int
     */
    public function getFailedLoginAttempts(string $ipAddress, int $minutes = 15): int
    {
        return CustomerAuthLog::where('ip_address', $ipAddress)
            ->where('action', 'login_failed')
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }
}