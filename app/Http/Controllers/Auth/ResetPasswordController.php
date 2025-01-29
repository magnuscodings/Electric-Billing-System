<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /**
     * Show the form to reset the password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Handle the reset password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        // Validate the password reset form input
        $this->validateResetForm($request);

        // Attempt to reset the password
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user) use ($request) {
                // Reset the user's password
                $user->password = Hash::make($request->password);
                $user->save();

                // Fire the password reset event
                event(new PasswordReset($user));
            }
        );

        // Check the response and redirect accordingly
        return $response == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', trans($response))
            : back()->withErrors(['email' => trans($response)]);
    }

    /**
     * Validate the password reset form input.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateResetForm(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required', 'min:8'],
        ]);
    }

    /**
     * Get the credentials for the password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Get the password broker to perform the reset.
     *
     * @return \Illuminate\Auth\Passwords\PasswordBroker
     */
    protected function broker()
    {
        return Password::broker();
    }

    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())
                    ->with('status', trans($response));
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans($response)]);
    }
}
