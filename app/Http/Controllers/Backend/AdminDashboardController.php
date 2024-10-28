<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
//use Illuminate\Support\Facades\Notification;
use App\Notifications\ClientAccountActivationNotification;
use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\Complain;

class AdminDashboardController extends Controller
{
    public function DashboardPage():View{
        return view('backend.pages.dashboard.dashboard-page');
    }


    public function TotalInfo(Request $request)
    {
        try {
            $totalClients = User::where('role', 'client')->count();
            $totalCustomers = User::where('role', 'user')->count();
            $totalFoods = Food::count();
            $totalOrders = Order::count();
            $totalComplaints = Complain::count();

            return response()->json([
                'status' => 'success',
                'totalClients' => $totalClients,
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


    public function UpdateClientAccount(Request $request, $client_id)
    {
        $client = User::find($client_id);
        if ($client) {
            $client->is_email_verified = $request->input('is_email_verified');
            $client->save();

            if ($request->input('is_email_verified') == 1) {
                $client->notify(new ClientAccountActivationNotification($client));
                return response()->json([
                    'status' => 'success',
                    'message' => 'Client account has been successfully activated!',
                ], 200);
            } else {
                $client->notify(new ClientAccountActivationNotification($client));
                return response()->json([
                    'status' => 'success',
                    'message' => 'Client account has been successfully deactivated!',
                ], 200);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Client not found'
        ], 404);
    }

    
    public function Logout(){
        return redirect('/admin/login')->withCookie(cookie()->forget('token'));
    }


}
