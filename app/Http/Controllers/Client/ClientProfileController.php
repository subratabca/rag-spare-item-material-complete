<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Complain;
use App\Models\Food;
use DB;


class ClientProfileController extends Controller
{
    public function ProfilePage():View
    { 
        return view('client.pages.profile.profile-page');
    }

    public function Profile(Request $request)
    {
        try {
            $email = $request->header('email');

            if (!$email) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Email header is missing'
                ], 400);
            }

            $user = User::where('email', $email)->first();

            if ($user) {
                $unreadNotifications = $user->unreadNotifications;
                $readNotifications = $user->readNotifications;

                return response()->json([
                    'status' => 'success',
                    'message' => 'Request Successful',
                    'data' => $user,
                    'unreadNotifications' => $unreadNotifications,
                    'readNotifications' => $readNotifications,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Client not found'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function UpdateProfile(Request $request)
    {
        try{
            $request->validate([
                'firstName' => 'required|string|min:3|max:50',
                'lastName' => 'required|string|min:3|max:50',
                'mobile' => 'required|string|min:11|max:50',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $email = $request->header('email');
            $id = $request->header('id');
            
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'User not found'
                ], 404);
            }

            $firstName = $request->input('firstName');
            $lastName = $request->input('lastName');
            $mobile = $request->input('mobile');

            if ($request->hasFile('image')) {
                $large_image_path = base_path('public/upload/client-profile/large/');
                $medium_image_path = base_path('public/upload/client-profile/medium/');
                $small_image_path = base_path('public/upload/client-profile/small/');

                if (!empty($user->image)) {
                    foreach (['large', 'medium', 'small'] as $size) {
                        $path = base_path("public/upload/client-profile/{$size}/" . $user->image);
                        if (file_exists($path)) {
                            unlink($path);
                        }
                    }
                }

                $image = $request->file('image');
                $manager = new ImageManager(new Driver());
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $img = $manager->read($image);

                $img->resize(100, 100)->save($large_image_path . $imageName);
                $img->resize(80, 80)->save($medium_image_path . $imageName);
                $img->resize(60, 60)->save($small_image_path . $imageName);

                $user->image = $imageName;
            }

            $user->update([
                'firstName'=>$firstName,
                'lastName'=>$lastName,
                'mobile'=>$mobile,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Request Successful',
            ],200);

        }catch (ValidationException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Validation errors',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


    public function PasswordPage():View
    {
        return view('client.pages.profile.password-change-page');
    }


    public function UpdatePassword(Request $request)
    {
        try {
            $request->validate([
                'oldpassword' => 'required|string|min:6',
                'newpassword' => 'required|string|min:6|confirmed', 
            ]);

            $email = $request->header('email');
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'User not found'
                ], 404);
            }

            $oldPassword = $request->input('oldpassword');
            $hashedPassword = $user->password;

            if (Hash::check($oldPassword, $hashedPassword)) {
                $newPassword = Hash::make($request->input('newpassword'));
                $user->password = $newPassword;
                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Password updated successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Old password is incorrect'
                ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


    public function ClientDetailsPage(Request $request)
    {
        $email = $request->header('email');
        $user = User::where('email', $email)->first();

        $notification_id = $request->query('notification_id');
        if ($notification_id) {
            $notification = $user->notifications()->where('id', $notification_id)->first();

            if ($notification && is_null($notification->read_at)) {
                $notification->markAsRead();
            }
        }
        
        return view('client.pages.profile.client-details');
    }


    public function ClientDetailsInfo($client_id)
    {
        try {
            $client = User::where('id', $client_id)
                ->where('role', 'client')
                ->withCount(['foods' => function ($query) {
                    $query->where('status', '!=', 'pending');
                }])
                ->withCount(['ordersBasedOnRole as total_orders'])
                ->withCount(['foods as total_complaints' => function ($query) {
                    $query->whereHas('order.complain');
                }])
                ->withCount(['ordersBasedOnRole as total_customers' => function ($query) {
                    $query->select(DB::raw('count(distinct user_id)'));
                }]) 
                ->first();

            if (!$client) { 
                return response()->json([
                    'status' => 'failed',
                    'message' => 'No client found with this ID',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $client
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving the customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
