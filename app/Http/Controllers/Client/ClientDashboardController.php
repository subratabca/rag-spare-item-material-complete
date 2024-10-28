<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\Complain;

class ClientDashboardController extends Controller
{
    public function DashboardPage():View{
        return view('client.pages.dashboard.dashboard-page');
    }


    public function TotalInfo(Request $request)
    {
        try {
            $clientId = $request->header('id');

            $totalFoods = Food::where('user_id', $clientId)->count();

            $totalOrders = Order::where('client_id', $clientId)->count();

            $totalCustomers = Order::where('client_id', $clientId)
                ->distinct('user_id') 
                ->count('user_id');

            $totalComplaints = Complain::whereHas('order', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })->count();

            return response()->json([
                'status' => 'success',
                'totalCustomers' => $totalCustomers,
                'totalFoods' => $totalFoods,
                'totalOrders' => $totalOrders,
                'totalComplaints' => $totalComplaints,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving totals',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function Logout(){
        return redirect('/client/login')->withCookie(cookie()->forget('token'));
    }

}
