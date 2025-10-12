@extends('layouts.auth')

@push('page-styles')
<style>
    /* Make the auth card centered and nicely sized */
    .auth-card {
        max-width: 800px; /* Adjust as needed */
        margin: auto;
    }
    @media (max-width: 991.98px) { /* Bootstrap lg breakpoint */
        .auth-card {
            max-width: 100%;
            padding: 0 15px;
        }
    }

    /* Center the divider line */
    .divider-hr {
        max-width: 400px;
        margin: 2rem auto;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center g-4">
    <!-- Admin/Staff Registration -->
    <div class="col-12 col-lg-6">
        <form class="card card-md auth-card" action="{{ route('register') }}" method="POST" autocomplete="off">
            @csrf

            <div class="card-body">
                <h2 class="card-title text-center mb-4">Staff/Admin Registration</h2>

                <x-input name="name" :value="old('name')" placeholder="Your name" required="true"/>
                <x-input name="email" :value="old('email')" placeholder="your@email.com" required="true"/>
                <x-input name="username" :value="old('username')" placeholder="Your username" required="true"/>
                <x-input name="password" :value="old('password')" placeholder="Password" required="true"/>
                <x-input name="password_confirmation" :value="old('password_confirmation')" placeholder="Password confirmation" required="true" label="Password Confirmation"/>

                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="terms-of-service" id="terms-of-service"
                               class="form-check-input @error('terms-of-service') is-invalid @enderror"
                        >
                        <span class="form-check-label">
                            Agree to the <a href="./terms-of-service.html" tabindex="-1">
                                terms and policy</a>.
                        </span>
                    </label>
                </div>

                <div class="form-footer">
                    <x-button type="submit" class="w-100">
                        {{ __('Create Staff/Admin Account') }}
                    </x-button>
                </div>
            </div>
        </form>

        <div class="text-center text-secondary mt-3">
            Staff/Admin? Already have account? <a href="{{ route('login') }}" tabindex="-1">
                Sign in
            </a>
        </div>
    </div>
</div>

<!-- Divider -->
<div class="row mt-4">
    <div class="col-12">
        <div class="text-center">
            <hr class="divider-hr">
            <p class="text-muted small">
                <strong>Staff/Admin:</strong> For employees who manage inventory, orders, and system settings
            </p>
        </div>
    </div>
</div>
@endsection
