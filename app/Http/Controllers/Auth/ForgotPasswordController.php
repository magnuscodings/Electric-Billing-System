<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
     /**
     * Show the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send the password reset link to the given email address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email
        $this->validateEmail($request);

        // Send the password reset link
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        // Handle the response
        return $response == Password::RESET_LINK_SENT
            ? back()->with('status', trans($response))
            : back()->withErrors(['email' => trans($response)]);
    }

    public function sendResetLinkEmailTest(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => __($status) // "We have emailed your password reset link!"
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => __($status) // "Unable to send reset link"
            ], 400);
        }
    }

    /**
     * Validate the email input.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);
    }

    /**
     * Get the password broker to send the reset link.
     *
     * @return \Illuminate\Auth\Passwords\PasswordBroker
     */
    protected function broker()
    {
        return Password::broker();
    }
}