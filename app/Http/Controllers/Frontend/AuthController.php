<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewCustomerRegistrationNotification;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Helper\JWTToken;
use App\Mail\EmailVerificationMail;
use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\View\View;
use App\Models\TermCondition;

class AuthController extends Controller
{
    public function TermsConditionsPage()
    {
        return view('frontend.pages.registration-terms-condition.customer-terms-condition-registration-page');
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
        return view('frontend.pages.auth.registration-page');
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

            $customer = User::create([
                'firstName' => $request->input('firstName'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'accept_registration_tnc' => $request->input('accept_registration_tnc'),
                'role' => 'user'
            ]);

            if ($customer) {
                $admin = User::where('role', 'admin')->first();
                $admin->notify(new NewCustomerRegistrationNotification($customer));

                Mail::to($customer->email)->send(new EmailVerificationMail($customer));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Registration success. We have sent you an activation link, please check your email.'
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



    public function VerifyCustomer(Request $request): View
    {
        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            if ($user->role === 'user' && $user->is_email_verified == 0) {
                $user->is_email_verified = 1;
                $user->save();

                return view('frontend.pages.auth.login-page')->with('message', 'Your account is activated. You can login now.');
            }
        } else {
            return view('frontend.pages.auth.login-page')->with('message', 'User not found.');
        }
    }


    public function LoginPage():View
    {
        return view('frontend.pages.auth.login-page');
    }


    public function Login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6'
            ]);

            $user = User::where('role', 'user')
                        ->where('email', $request->input('email'))
                        ->select('firstName', 'id', 'password', 'is_email_verified')
                        ->first();

            if ($user !== null) {
                if ($user->is_email_verified == 0) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'You have to activate your account. Please check your email.'
                    ], 403);
                }

                if (Hash::check($request->input('password'), $user->password)) {
                    $token = JWTToken::CreateToken($request->input('email'), $user->id, $user->role);

                    $intendedUrl = session('url.intended', '/user/dashboard');

                    return response()->json([
                        'status' => 'success',
                        'message' => 'User Login Successful',
                        'token' => $token,
                        'redirect' => $intendedUrl
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


    public function SendOtpPage():View
    {
        return view('frontend.pages.auth.send-otp-page');
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
        return view('frontend.pages.auth.verify-otp-page');
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
        return view('frontend.pages.auth.reset-password-page');
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
