<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Complain;

class ClientCustomerListController extends Controller
{
    public function CustomerPage()
    {
        return view('client.pages.customer.customer-list');
    }


    public function CustomerList(Request $request)
    {
        try {
            $client_id = $request->header('id');
            $customers = User::where('role', 'user')
                ->whereHas('orders', function ($query) use ($client_id) {
                    $query->where('client_id', $client_id);
                })
                ->with(['orders' => function ($query) use ($client_id) {
                    $query->where('client_id', $client_id);
                }, 'complains'])
                ->withCount(['orders' => function ($query) use ($client_id) {
                    $query->where('client_id', $client_id);
                }, 'complains'])
                ->latest()
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $customers
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function CustomerDetailsPage()
    {
        return view('client.pages.customer.customer-details');
    }


    public function CustomerDetailsInfo($customer_id)
    {
        try {
            $customer = User::where('id', $customer_id)
                ->where('role', 'user')
                ->withCount(['orders', 'complains'])  
                ->first();

            return response()->json([
                'status' => 'success',
                'data' => $customer
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving the customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function OrderListPageByCustomer()
    {
        return view('client.pages.customer.order-list-by-customer');
    }


    public function OrderListByCustomer($customer_id)
    {
        try {
            $order = Order::with('user','food','food.user','complain')->where('user_id',$customer_id)->get();
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


    public function ComplainListPageByCustomer()
    {
        return view('client.pages.customer.complain-list-by-customer');
    }


    public function ComplainListByCustomer($customer_id)
    {
        try {
            $complains = Complain::with(['order', 'food.user', 'user'])->where('user_id', $customer_id)->latest()->get();

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

}