<?php
namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewClientRegistrationNotification;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\View\View;
use App\Models\TermCondition;


class ClientAuthController extends Controller
{
    public function TermsConditionsPage()
    {
        return view('frontend.pages.registration-terms-condition.client-terms-condition-registration-page');
    }

    public function TermsConditionsInfo($name)
    {
        try {
            $termsCondition = TermCondition::where('name', str_replace('_', ' ', $name))->first();

            if ($termsCondition) {
                return response()->json([
                    'status' => 'success',
                    'data' => $termsCondition
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'T&C not found.'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to retrieve Terms & Conditions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function RegistrationPage():View
    {
        return view('client.pages.auth.registration-page');
    }


    public function Registration(Request $request)
    {
        try {
            $request->validate([
                'firstName' => 'required|string|max:50',
                'email' => 'required|string|email|max:50|unique:users,email',
                'password' => 'required|string|min:6',
                'accept_registration_tnc' => 'required|boolean',
            ]);

            $client = User::create([
                'firstName' => $request->input('firstName'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'accept_registration_tnc' => $request->input('accept_registration_tnc'),
                'role' => 'client'
            ]);

            if ($client) {
                $admin = User::where('role', 'admin')->first();
                $admin->notify(new NewClientRegistrationNotification($client));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Your registration was successful. We will notify you via email once your account has been activated.'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function LoginPage():View
    {
        return view('client.pages.auth.login-page');
    }


    public function Login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6'
            ]);

            $user = User::where('role', 'client')
                    ->where('email', $request->input('email'))
                    ->select('firstName', 'id', 'password', 'role', 'is_email_verified')
                    ->first();

            if ($user !== null) {
                if ($user->is_email_verified == 0) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'You need to wait until your account is activated.'
                    ], 403); 
                }

                if (Hash::check($request->input('password'), $user->password)) {
                    $token = JWTToken::ClientCreateToken($request->input('email'), $user->id, $user->role);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'User Login Successful',
                        'token' => $token
                    ], 200)->cookie('token', $token, 60 * 24 * 30);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Email or Password is Invalid'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'User not found'
                ], 404);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function oldLogin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6'
            ]);

            $user = User::where('role', 'client')
                    ->where('email', $request->input('email'))
                    ->select('firstName', 'id', 'password', 'role')
                    ->first();

            if ($user !== null && Hash::check($request->input('password'), $user->password)) {
                $token = JWTToken::ClientCreateToken($request->input('email'), $user->id, $user->role);

                return response()->json([
                    'status' => 'success',
                    'message' => 'User Login Successful',
                    'token' => $token
                ], 200)->cookie('token', $token, 60 * 24 * 30);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Email or Password is Invalid'
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function SendOtpPage():View{
        return view('client.pages.auth.send-otp-page');
    }


    public function SendOTPCode(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $email = $request->input('email');
            $otp = rand(1000, 9999);

            $user = User::where('email', '=', $email)->first();

            if ($user) {
                Mail::to($email)->send(new OTPMail($otp));
                User::where('email', '=', $email)->update(['otp' => $otp]);

                return response()->json([
                    'status' => 'success',
                    'message' => '4 Digit OTP Code has been sent to your email!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'User not found',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred. Please try again later.',
            ], 500);
        }
    }


    public function VerifyOTPPage():View{
        return view('client.pages.auth.verify-otp-page');
    }


    public function VerifyOTP(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email|max:50|exists:users,email',
                'otp' => 'required|string|size:4'
            ]);

            $email = $request->input('email');
            $otp = $request->input('otp');
            $user = User::where('email', '=', $email)->where('otp', '=', $otp)->first();

            if ($user !== null) {
                User::where('email', '=', $email)->update(['otp' => '0']);
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP Verification Successful',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Unauthorized',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred. Please try again later.',
            ], 500);
        }
    }


    public function ResetPasswordPage():View{
        return view('client.pages.auth.reset-password-page');
    }


    public function ResetPassword(Request $request){
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6'
            ]);

            $email = $request->input('email');
            $password = Hash::make($request->input('password'));

            $user = User::where('email', '=', $email)->first();

            if ($user) {
                User::where('email', '=', $email)->update(['password' => $password]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password reset successful',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'User not found',
                ], 404); 
            }

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'fail',
                'errors' => $e->errors(),
            ], 422);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

}
