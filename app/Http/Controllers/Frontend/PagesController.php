<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Models\About;
use App\Models\Food;
use App\Models\Contact;

use Carbon\Carbon;

class PagesController extends Controller
{

    public function AboutPage()
    {
        return view('frontend.pages.about-page');
    }

    public function AboutPageInfo(): JsonResponse
    {
        try {
            $data = About::first();

            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Request Successful',
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'No data found',
                    'data' => []
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage() 
            ], 500);
        }
    }


    public function ContactPage()
    {
        return view('frontend.pages.contact-us-page');
    }


    public function StoreContactInfo(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|min:3|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'message' => 'required|string',
            ]);

            $contact = Contact::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'message' => $validated['message'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Message has been sent successfully.',
                'data' => $contact,
            ], 201);

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


    public function AvailableFoodPage()
    {
        return view('frontend.pages.available-food-page');
    }


    public function AvailableFoodList(Request $request,$id=null)
    {
        try {
            $currentDate = Carbon::now(new \DateTimeZone('Asia/Dhaka'));
            $data = Food::where('expire_date', '>=', $currentDate)
                        ->where('status', 'published')
                        ->orWhere('status', 'processing')
                        ->latest()
                        ->paginate(6);

            if ($data->isNotEmpty()) { 
                return response()->json([
                    'status' => 'success',
                    'message' => 'Request Successful',
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Food Not Found'
                ], 404); 
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred'
            ], 500);
        }
    }


    public function FoodDetailsPage()
    {
        return view('frontend.pages.food-details-page');
    }


    public function FoodDetailsInfo($id): JsonResponse
    {
        try {
            $data = Food::with('user', 'foodImages')->find($id);

            if (!$data) { 
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Food Not Found'
                ], 404); 
            }

            $currentDate = Carbon::now(new \DateTimeZone('Asia/Dhaka'));
            $relatedData = Food::where('expire_date', '>=', $currentDate)
                                ->where('status', 'published')
                                ->where('id', '!=', $id)
                                ->inRandomOrder()
                                ->limit(7)
                                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Request Successful',
                'data' => $data,
                'relatedData' => $relatedData
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred'
            ], 500);
        }
    }


    public function getAvailableFoodByDate(Request $request, $date)
    {
        $timezone = new \DateTimeZone('Asia/Dhaka');
        $date = Carbon::parse($date, $timezone)->format('Y-m-d');

        $foods = Food::where('expire_date', '>=', $date)
                ->whereDate('collection_date', $date)
                ->where(function ($query) {
                    $query->where('status', 'published')
                          ->orWhere('status', 'processing');
                })
                ->latest()
                ->paginate(6);

        return response()->json([
            'data' => $foods,
        ]);
    }

    public function searchFood(Request $request)
    {
        $query = $request->input('query');
        $currentDate = Carbon::now(new \DateTimeZone('Asia/Dhaka'));

        $foods = Food::where('name', 'like', "%{$query}%") 
                     ->where('expire_date', '>=', $currentDate)
                     ->where(function ($q) {
                         $q->where('status', 'published')
                           ->orWhere('status', 'processing');
                     })
                     ->get(['name']); 

        return response()->json($foods);
    }

}