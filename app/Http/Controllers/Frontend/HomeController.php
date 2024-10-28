<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\SiteSetting;
use App\Models\Food;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function HomePage()
    {
        return view('frontend.pages.home-page');
    }


    public function SettingList()
    {
        try {
            $data = SiteSetting::first(); 
            return response()->json([
                'status' => 'success',
                'message' => 'Request Successful',
                'data' => $data
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage() 
            ], 500);
        }
    }


    public function FoodList(Request $request, $id = null)
    {
        try {
            $currentDate = Carbon::now(new \DateTimeZone('Asia/Dhaka'));
            $foods = Food::where('expire_date', '>=', $currentDate)
                ->where('status', 'published')
                ->orWhere('status', 'processing')
                ->latest()
                ->paginate(6);

            return response()->json([
                'status' => 'success',
                'message' => 'Request Successful',
                'foods' => $foods,
                'total' => $foods->total() // Add total count
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred'
            ], 500);
        }
    }

    public function searchFood(Request $request)
    {
        try {
            $query = $request->input('query');
            $currentDate = Carbon::now(new \DateTimeZone('Asia/Dhaka'));

            $foods = Food::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('address', 'like', "%{$query}%");
                })
                ->where('expire_date', '>=', $currentDate)
                ->where(function ($q) {
                    $q->where('status', 'published')
                      ->orWhere('status', 'processing');
                })
                ->get();

            return response()->json([
                'status' => 'success',
                'foods' => $foods,
                'total' => $foods->count() // Add total count
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while searching for food.'
            ], 500);
        }
    }



}