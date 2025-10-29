<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'user_type',
        'attempted_at',
        'successful',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'successful' => 'boolean',
    ];

    /**
     * Scope for customer login attempts
     */
    public function scopeCustomer($query)
    {
        return $query->where('user_type', 'customer');
    }

    /**
     * Scope for admin/staff login attempts
     */
    public function scopeAdmin($query)
    {
        return $query->where('user_type', 'admin');
    }

    /**
     * Scope for successful attempts
     */
    public function scopeSuccessful($query)
    {
        return $query->where('successful', true);
    }

    /**
     * Scope for failed attempts
     */
    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }

    /**
     * Scope for recent attempts (within the last 5 minutes)
     */
    public function scopeRecent($query, $minutes = 5)
    {
        return $query->where('attempted_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope for attempts by email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope for attempts by IP address
     */
    public function scopeByIpAddress($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }
}