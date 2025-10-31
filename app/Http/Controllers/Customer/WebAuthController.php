<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use App\Services\CustomerAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Rules\UniqueEmailAcrossTables;

class WebAuthController extends Controller
{
    protected $authService;

    public function __construct(CustomerAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Display customer registration view
     */
    public function showRegistrationForm()
    {
        return view('auth.customer.register');
    }

    /**
     * Handle customer registration
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s.\-\']+$/',
            'email' => ['required', 'string', 'email', 'max:255', new UniqueEmailAcrossTables],
            'username' => 'required|string|max:255|unique:customers',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|regex:/^\+63\d{10}$/|unique:customers',
            'address' => 'required|string|max:500',
        ], [
            'name.regex' => 'The name may only contain letters, spaces, periods, hyphens, and apostrophes.',
            'phone.regex' => 'The phone number must start with +63 and be exactly 11 digits.',
            'phone.unique' => 'This phone number is already registered.',
        ]);

        // Process phone number - if it starts with 09, convert to +63
        $requestData = $request->all();
        if (isset($requestData['phone']) && preg_match('/^09\d{9}$/', $requestData['phone'])) {
            $requestData['phone'] = '+63' . substr($requestData['phone'], 1);
        }

        $result = $this->authService->createCustomerAccount($requestData);

        if ($result['success']) {
            // Log in the customer after registration using web_customer guard
            Auth::guard('web_customer')->login($result['customer']);
            $request->session()->regenerate();
            $request->session()->save(); // Force session save
            
            // Remove email verification step: always redirect to dashboard
            return redirect()->route('customer.dashboard')
                ->with('success', 'Account created successfully!');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * Display customer login view
     */
    public function showLoginForm()
    {
        return view('auth.customer.login');
    }

    /**
     * Handle customer login
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->authService->loginCustomer($request->login, $request->password);

        if ($result['success']) {
            // Log in the customer for web session using web_customer guard
            Auth::guard('web_customer')->login($result['customer']);
            $request->session()->regenerate();

            return redirect()->route('customer.dashboard')
                ->with('success', 'Welcome back!');
        }

        // Pass lockout information to the session
        if (isset($result['lockout_seconds'])) {
            return back()->withErrors(['login' => $result['message']])
                ->with('lockout_seconds', $result['lockout_seconds']);
        }

        // For other cases, just show the error message
        return back()->withErrors(['login' => $result['message']]);
    }

    /**
     * Handle customer logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $customer = $request->user();
        
        if ($customer instanceof Customer) {
            $this->authService->logoutCustomer($customer);
        }

        Auth::guard('web_customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}