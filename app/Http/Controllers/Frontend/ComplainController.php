<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Notifications\FoodComplainNotification;
use App\Notifications\FoodComplainFeedbackNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Notification;
use Exception;
use App\Models\Complain;
use App\Models\ComplainConversation;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ComplainController extends Controller
{

    public function FoodComplainPage($order_id){
        return view('frontend.pages.complain.food-complain-page',compact('order_id'));
    }


    public function uploadEditorImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(100, 100)->save(public_path('upload/complain_images/customer/' . $imageName));
            $url = asset('upload/complain_images/customer/' . $imageName);
            return response()->json(['image_url' => $url], 200);
        }
    }


    public function deleteEditorImage(Request $request)
    {
        $imageUrl = $request->input('image_url');
        $imagePath = parse_url($imageUrl, PHP_URL_PATH);
        $fullImagePath = public_path($imagePath);

        if (File::exists($fullImagePath)) {
            File::delete($fullImagePath);
            return response()->json(['status' => 'success', 'message' => 'Image deleted successfully']);
        }

        return response()->json(['status' => 'error', 'message' => 'Image not found'], 404);
    }


    public function StoreComplainInfo(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|min:20|max:10000',
                'order_id' => 'required|integer|exists:orders,id',
            ]);

            $user_id = $request->header('id');

            if (!$user_id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'User ID is missing in the request headers.',
                ], 400);
            }

            $order_id = $request->input('order_id');

            $existingComplain = Complain::where('order_id', $order_id)
                ->where('user_id', $user_id)
                ->first();

            if ($existingComplain) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'You have already submitted a complaint for this order.',
                ], 400);
            }

            $food_id = Order::where('id', $order_id)->value('food_id');

            $currentDateTime = Carbon::now();
            $cmp_date = $currentDateTime->format('d F Y');
            $cmp_time = $currentDateTime->format('h:i:s A');

            $messageContent = $validated['message'];

            $complain = Complain::create([
                'order_id' => $order_id,
                'food_id' => $food_id,
                'user_id' => $user_id,
                'message' => $messageContent,
                'cmp_date' => $cmp_date,
                'cmp_time' => $cmp_time,
            ]);

            if ($complain) {
                $admin = User::where('role', 'admin')->first();
                if ($admin) {
                    $admin->notify(new FoodComplainNotification($complain));
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Complain has been sent successfully.',
                    'data' => $complain,
                ], 201);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Failed to create complain.',
                ], 500);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function ComplainPage()
    {
        return view('frontend.pages.complain.complain-page');
    }


    public function ComplainList(Request $request)
    {
        try {
            $user_id = $request->header('id');
            $complains = Complain::with(['food','food.user','conversations'])->where('user_id', $user_id)->latest()->get();

            return response()->json([
                'status' => 'success',
                'data' => $complains
            ], 200); 

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving complaints',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function ComplainDetailsPage(Request $request)
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
        
        return view('frontend.pages.complain.complain-details-page');
    }


    public function ComplainDetailsInfo(Request $request, $complain_id)
    {
        try {
            $complain = Complain::with(['order', 'food', 'food.user', 'food.foodImages', 'user', 'conversations'])
                ->where('id', $complain_id)
                ->first();

            if (!$complain) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Complain information not found'
                ], 404);
            }

            $user_id = $request->header('id');
            $user = User::findOrFail($user_id);

            if ($user) {
                $notification = $user->notifications()
                    ->where('notifiable_id', $user_id)
                    ->where('data->complain_id', $complain_id)  
                    ->first();

                if ($notification && is_null($notification->read_at)) {
                    $notification->markAsRead();
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $complain
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving complain information',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function FoodComplainReplyPage($complain_id)
    {
        return view('frontend.pages.complain.complain-reply-page');
    }


    public function StoreComplainReplyInfo(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reply_message' => 'required|string|min:20|max:500',
                'complain_id' => 'required|exists:complains,id',
            ]);

            $user_id = $request->header('id');
            $complain_id = $validated['complain_id'];

            $complain = Complain::where('id', $complain_id)
                ->where('user_id', $user_id)
                ->first();

            if (!$complain) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Complaint not found or unauthorized.',
                ], 404);
            }

            $lastConversation = ComplainConversation::where('complain_id', $complain_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastConversation || $lastConversation->sender_role !== 'client') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'You cannot reply until the client has responded.',
                ], 403);
            }

            $customerReplyCount = ComplainConversation::where('complain_id', $complain_id)
                ->where('sender_id', $user_id)
                ->where('sender_role', 'user')
                ->count();

            if ($customerReplyCount >= 3) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'You have reached the maximum number of replies for this complaint.',
                ], 403);
            }

            $complainConversation = ComplainConversation::create([
                'complain_id' => $complain_id,
                'sender_id' => $user_id,
                'reply_message' => $validated['reply_message'],
                'sender_role' => 'user',
            ]);

            if ($complainConversation) {
                $admin = User::where('role', 'admin')->first();
                $client = $complain->food->user;

                if ($admin && $client) {
                    $admin->notify(new FoodComplainFeedbackNotification($complainConversation));
                    $client->notify(new FoodComplainFeedbackNotification($complainConversation));

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Complain feedback has been sent successfully.',
                        'data' => $complainConversation,
                    ], 201);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Admin or Client not found.',
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Failed to create complain feedback.',
                ], 500);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}