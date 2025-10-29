<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email'); // Email or username used for login
            $table->string('ip_address', 45); // IPv4 or IPv6
            $table->string('user_type'); // 'customer' or 'admin'
            $table->timestamp('attempted_at')->useCurrent();
            $table->boolean('successful')->default(false);
            $table->timestamps();
            
            $table->index(['email', 'user_type']);
            $table->index(['ip_address', 'attempted_at']);
            $table->index(['email', 'attempted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};