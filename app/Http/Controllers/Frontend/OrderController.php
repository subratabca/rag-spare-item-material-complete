<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\FoodRequestNotification;
use Illuminate\Validation\ValidationException; 
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Food;
use App\Models\User;
use App\Models\Complain;

class OrderController extends Controller
{
    public function StoreFoodRequest(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:food,id',
            ]);

            $user_id = $request->header('id'); 
            $food_id = $request->input('id');

            $user = User::find($user_id);
            if (!$user) {
                return response()->json([
                    'status' => 'unauthorized',
                    'message' => 'User not found. Need to login'
                ], 401);
            }

            $currentDateTime = Carbon::now();
            $order_date = $currentDateTime->format('d F Y');
            $order_time = $currentDateTime->format('h:i:s A');

            $existingOrder = Order::where('user_id', $user_id)
                                  ->where('order_date', $order_date)
                                  ->exists();

            if ($existingOrder) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'You have already created an order for today.',
                ], 400); 
            }

            $food = Food::with('user')->findOrFail($food_id);
            $client = $food->user;

            if ($client->role !== 'client') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'The food does not belong to a client user.',
                ], 400);
            }

            $order = Order::create([
                'user_id' => $user_id, 
                'food_id' => $food_id,
                'client_id' => $client->id, 
                'order_date' => $order_date,
                'order_time' => $order_time
            ]);

            if ($order) {
                $food->update(['status' => 'processing']);

                $client->notify(new FoodRequestNotification($order));

                return response()->json([
                    'status' => 'success',
                    'message' => 'Food request accepted successfully.',
                    'data' => $order
                ], 201);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Failed to create order.',
                ], 500);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Food request failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function OrderPage()
    {
        return view('frontend.pages.order.order-page');
    }

    public function OrderList(Request $request)
    {
        try {
            $id = $request->header('id');
            $order = Order::with('user','food','food.user','complain')->where('user_id',$id)->get();
            return response()->json([
                'status' => 'success',
                'data' => $order
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Order information not found',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function OrderDetailsPage(Request $request)
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
        
        return view('frontend.pages.order.order-details-page');
    }


    public function OrderDetailsInfo(Request $request, $order_id)
    {
        try {
            $order = Order::with('user', 'food', 'food.foodImages', 'food.user')
                ->findOrFail($order_id);

            $user_id = $request->header('id');
            $user = User::findOrFail($user_id);

            //$notificationId = $request->query('notification_id');

            if ($user) {
                $notification = $user->notifications()
                    ->where('notifiable_id', $user_id)
                    ->where('data->order_id', $order_id)  
                    ->first();

                if ($notification && is_null($notification->read_at)) {
                    $notification->markAsRead();
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $order,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Order information not found',
                'error' => $e->getMessage()
            ], 500); 
        }
    }


 
}


