<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\ApproveFoodRequestNotification;
use App\Notifications\FoodDeliveryNotification;
use Illuminate\Support\Facades\Notification;
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Food;
use App\Models\User;

class ClientOrderController extends Controller
{
    public function OrderPage(){
        return view('client.pages.order.order-list');
    }

    public function OrderList(Request $request)
    {
        try {
            $user_id = $request->header('id');
            $orders = Order::whereHas('food', function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->with('user', 'food')
            ->latest()
            ->get();

            return response()->json([
                'status' => 'success',
                'data' => $orders
            ], 200); 

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving orders',
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

        return view('client.pages.order.order-details');
    }
    
    public function OrderDetails($id)
    {
        try {
            $order = Order::with('user','food','food.user','food.foodImages')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $order
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Order information not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }


    public function ApproveFoodRequest(Request $request)
    {
        try {
            $accept_tnc = $request->input('accept_tnc');
            $order_id = $request->input('id');
            $order = Order::with('user','food')->where('id', $order_id)->first();

            $currentDateTime = Carbon::now();
            $approve_date = $currentDateTime->format('d F Y');
            $approve_time = $currentDateTime->format('h:i:s A');

            $order->update([
                'status' => 'approved food request',
                'accept_order_request_tnc' => $request->input('accept_tnc'),
                'approve_date' => $approve_date,
                'approve_time' => $approve_time
            ]);

            $food = $order->food;
            if ($order->user->role === 'user') {
                $order->user->notify(new ApproveFoodRequestNotification($food,$order));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Food request approved successfully.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Order not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Status update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function DeliveredFoodRequest(Request $request)
    {
        try {
            $accept_tnc = $request->input('accept_tnc');
            $order_id = $request->input('id');
            $order = Order::with('user','food')->where('id', $order_id)->first();

            if (is_null($order)) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Order not found.',
                ], 404);
            }

            $currentDateTime = Carbon::now();
            $delivery_date = $currentDateTime->format('d F Y');
            $delivery_time = $currentDateTime->format('h:i:s A');

            $order->update([
                'status' => 'completed',
                'accept_food_deliver_tnc' => $request->input('accept_tnc'),
                'delivery_date' => $delivery_date,
                'delivery_time' => $delivery_time
            ]);

            $food = $order->food;
            if ($food) {
                $food->update(['status' => 'completed']);

                if ($order->user->role === 'user') {
                    $order->user->notify(new FoodDeliveryNotification($food,$order));
                }

                $admin = User::where('role', 'admin')->first();
                $admin->notify(new FoodDeliveryNotification($food,$order));

            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Food item not found.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Food delivery completed successfully.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Status update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function CancelFoodRequest(Request $request)
    {
        try {
            $order_id = $request->input('id');
            $order = Order::find($order_id);

            if (is_null($order)) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Order not found.',
                ], 404);
            }

            $currentDateTime = Carbon::now();
            $cancel_date = $currentDateTime->format('d F Y');
            $cancel_time = $currentDateTime->format('h:i:s A');

            $order->update([
                'status' => 'cancel',
                'cancel_date' => $cancel_date,
                'cancel_time' => $cancel_time
            ]);

            $food_id = $order->food_id;
            $food = Food::find($food_id);
            if ($food) {
                $food->update(['status' => 'published']);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Food item not found.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cancell food request successfully.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Status update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}