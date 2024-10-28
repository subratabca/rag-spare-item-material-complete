<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException; 
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Food;
use App\Models\Complain;

class DashboardController extends Controller
{
    public function DashboardPage():View
    {
        return view('frontend.pages.dashboard.dashboard-page');
    }


    public function TotalInfo(Request $request)
    {
        try {
            $userId = $request->header('id');

            $totalOrders = Order::where('user_id', $userId)->count();

            $totalComplaints = Complain::whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->count();

            return response()->json([
                'status' => 'success',
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
    

    public function Logout()
    {
        return redirect('/')->withCookie(cookie()->forget('token'));
    }

}