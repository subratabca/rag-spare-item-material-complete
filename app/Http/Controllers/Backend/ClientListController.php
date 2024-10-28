<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Complain;
use App\Models\Food;
use App\Models\FoodImage;

class ClientListController extends Controller
{
    public function ClientPage()
    {
        return view('backend.pages.client.client-list');
    }


    public function ClientList(Request $request)
    {
        try {
            $clients = User::where('role', 'client')
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
                ->latest()
                ->get();

            $clients = $clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'firstName' => $client->firstName,
                    'lastName' => $client->lastName,
                    'email' => $client->email,
                    'mobile' => $client->mobile,
                    'image' => $client->image,
                    'is_email_verified' => $client->is_email_verified,
                    'created_at' => $client->created_at,
                    'non_pending_food_count' => $client->foods_count,
                    'total_orders' => $client->total_orders, 
                    'total_complaints' => $client->total_complaints, 
                    'total_customers' => $client->total_customers, 
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $clients
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving clients',
                'error' => $e->getMessage()
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
        
        return view('backend.pages.client.client-details');
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


    public function OrderListPageByClient()
    {
        return view('backend.pages.client.order-list-by-client');
    }
    

    public function OrderListByClient($client_id)
    {
        try {
            $orders = Order::with('user', 'food')->where('client_id',$client_id)->latest()->get();

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


    public function ComplainListPageByClient()
    {
        return view('backend.pages.client.complain-list-by-client');
    }


    public function ComplainListByClient($client_id)
    {
        try {
            $complains = Complain::with(['order', 'food.user', 'user'])
                ->whereHas('order', function ($query) use ($client_id) {
                    $query->where('client_id', $client_id);
                })
                ->latest()
                ->get();

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


    public function CustomerListPageByClient()
    {
        return view('backend.pages.client.customer-list-by-client');
    }


    public function CustomerListByClient($client_id)
    {
        try {
            $customerIds = Order::where('client_id', $client_id)
                ->distinct('user_id')
                ->pluck('user_id'); 

            $customers = User::whereIn('id', $customerIds)
                ->where('role', 'user') 
                 ->withNonPendingFoodCount()
                 ->latest()
                ->get();

            $customers = $customers->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'firstName' => $customer->firstName,
                    'lastName' => $customer->lastName,
                    'email' => $customer->email,
                    'mobile' => $customer->mobile,
                    'image' => $customer->image,
                    'created_at' => $customer->created_at,
                    'non_pending_food_count' => $customer->foods_count, 
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $customers
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving clients',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function FoodListPageByClient()
    {
        return view('backend.pages.client.food-list-by-client');
    }


    public function FoodListByClient($client_id)
    {
        try {
            $foods = Food::with('user')->where('user_id', $client_id)->latest()->get();

            return response()->json([
                'status' => 'success',
                'data' => $foods
            ], 200); 

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving foods',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    private function deleteImagesFromHTML($htmlContent)
    {
        preg_match_all('/<img[^>]+src="([^">]+)"/', $htmlContent, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $imageUrl) {
                $imagePath = ltrim(parse_url($imageUrl, PHP_URL_PATH), '/');
                $fullImagePath = public_path($imagePath);
                if (File::exists($fullImagePath)) {
                    File::delete($fullImagePath);
                }
            }
        }
    }


    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $client_id = $request->input('client_id');
            $client = User::where('role', 'client')->findOrFail($client_id);

            $client_profile_paths = [
                'large' => base_path('public/upload/client-profile/large/'),
                'medium' => base_path('public/upload/client-profile/medium/'),
                'small' => base_path('public/upload/client-profile/small/')
            ];

            $food_image_paths = [
                'large' => base_path('public/upload/food/large/'),
                'medium' => base_path('public/upload/food/medium/'),
                'small' => base_path('public/upload/food/small/'),
                'multiple' => base_path('public/upload/food/multiple/')
            ];

            if ($client->foods) {
                foreach ($client->foods as $food) {
                    if ($food->order && $food->order->complain) {
                        foreach ($food->order->complain->conversations as $conversation) {
                            if (!empty($conversation->reply_message)) {
                               $this->deleteImagesFromHTML($conversation->reply_message);
                            }
                            $conversation->delete(); 
                        }

                        if (!empty($food->order->complain->message)) {
                            $this->deleteImagesFromHTML($food->order->complain->message);
                        }

                        $food->order->complain->delete(); 
                    }

                    $food->order?->delete();
                }


                foreach ($client->foods as $food) {
                    foreach ($food->foodImages as $foodImage) {
                        $imageFile = $food_image_paths['multiple'] . $foodImage->image;
                        if (file_exists($imageFile)) {
                            unlink($imageFile); 
                        }
                        $foodImage->delete();
                    }

                    foreach (['large', 'medium', 'small'] as $size) {
                        $foodImagePath = $food_image_paths[$size] . $food->image;
                        if (file_exists($foodImagePath)) {
                            unlink($foodImagePath);
                        }
                    }

                    $food->delete(); 
                }
            }

            if (!empty($client->image)) {
                foreach ($client_profile_paths as $path) {
                    $imagePath = $path . $client->image;
                    if (file_exists($imagePath)) {
                        unlink($imagePath); 
                    }
                }
            }

            $client->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Client, profile images, related orders, food items, complains, and conversations deleted successfully.'
            ], 200);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Client not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}