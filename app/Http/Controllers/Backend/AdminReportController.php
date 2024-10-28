<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use App\Models\Order;

class AdminReportController extends Controller
{
    public function TodaysReportPage()
    {
        return view('backend.pages.report.today-report-page');
    }

    public function TodaysOrderInfo(Request $request)
    {
        try {
            $today = Carbon::now()->format('d F Y');
            $orders = Order::with('user', 'food')->where('order_date', $today)->latest()->get();

            $totalOrders = $orders->count();
            $totalCompletedOrders = $orders->where('status', 'completed')->count();
            $totalPendingOrders = $orders->where('status', 'pending')->count();
            $totalApprovedFoodRequestOrders = $orders->where('status', 'approved food request')->count();
            $totalCanceledOrders = $orders->where('status', 'cancel')->count();

            return response()->json([
                'status' => 'success',
                'data' => $orders,
                'total_orders' => $totalOrders,
                'total_completed_orders' => $totalCompletedOrders,
                'total_pending_orders' => $totalPendingOrders,
                'total_approved_food_request_orders' => $totalApprovedFoodRequestOrders,
                'total_canceled_orders' => $totalCanceledOrders,
            ], 200); 

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function ReportSearchPage(Request $request)
    {
       return view('backend.pages.report.search-report-page');
    }

    public function OrderBySearch(Request $request)
    {
        try {
            $request->validate([
                'date' => 'nullable|date',           
                'start_date' => 'nullable|date',     
                'end_date' => 'nullable|date',       
            ]);

            if ($request->has('date')) {
                $date = Carbon::parse($request->date)->format('d F Y');
                $orders = Order::with('user', 'food')
                    ->where('order_date', $date)
                    ->latest()
                    ->get();

            } elseif ($request->has('start_date') && $request->has('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                
                $orders = Order::with('user', 'food')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->latest()
                    ->get();
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Please provide a valid date or date range',
                ], 400); 
            }

            $totalOrders = $orders->count();
            $totalCompletedOrders = $orders->where('status', 'completed')->count();
            $totalPendingOrders = $orders->where('status', 'pending')->count();
            $totalApprovedFoodRequestOrders = $orders->where('status', 'approved food request')->count();
            $totalCanceledOrders = $orders->where('status', 'cancel')->count();

            return response()->json([
                'status' => 'success',
                'data' => $orders,
                'total_orders' => $totalOrders,
                'total_completed_orders' => $totalCompletedOrders,
                'total_pending_orders' => $totalPendingOrders,
                'total_approved_food_request_orders' => $totalApprovedFoodRequestOrders,
                'total_canceled_orders' => $totalCanceledOrders,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while searching for orders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    
}