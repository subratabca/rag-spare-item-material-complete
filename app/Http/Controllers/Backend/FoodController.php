<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Notifications\FoodPublishNotification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification; 
use Exception;
use App\Models\User;
use App\Models\Food;
use App\Models\FoodImage;
use GuzzleHttp\Client;


class FoodController extends Controller
{
    public function FoodPage()
    {
        return view('backend.pages.food.food-list');
    }
    

    public function index(Request $request)
    {
        try {
            $foods = Food::latest()->get();

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


    public function CreatePage()
    {
        return view('backend.pages.food.create');
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|string',
                'gradients' => 'required|string',
                'expire_date' => 'required|date',
                'description' => 'required|string|min:20',
                'collection_date' => 'required|date',
                'start_collection_time' => 'required|date_format:H:i',
                'end_collection_time' => 'required|date_format:H:i',
                'address' => 'required|string|min:3|max:255',
                'accept_tnc' => 'required|boolean',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'multi_images' => 'required|array',
                'multi_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', 
            ]);

            $user_id = $request->header('id');
            $address = $request->input('address');
            $geoData = $this->getCoordinatesFromAddress($address);

            if (!$geoData) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Unable to fetch coordinates for the provided address.'
                ], 400);
            }

            $latitude = $geoData['latitude'];
            $longitude = $geoData['longitude'];
            
            $uploadPath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $manager = new ImageManager(new Driver());
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $img = $manager->read($image);
                $img->resize(570, 380)->save(base_path('public/upload/food/large/' . $imageName));
                $img->resize(388, 472)->save(base_path('public/upload/food/medium/' . $imageName));
                $img->resize(346, 415)->save(base_path('public/upload/food/small/' . $imageName));
                $uploadPath = $imageName;
            }

            $gradientsArray = json_decode($request->input('gradients'), true);
            $gradientsString = implode(',', array_column($gradientsArray, 'value'));

            $food = Food::create([
                'name' => $request->input('name'),
                'gradients' => $gradientsString,
                'expire_date' => $request->input('expire_date'),
                'description' => $request->input('description'),
                'collection_date' => $request->input('collection_date'),
                'start_collection_time' => $request->input('start_collection_time'),
                'end_collection_time' => $request->input('end_collection_time'),
                'address' => $request->input('address'),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'accept_tnc' => $request->input('accept_tnc'),
                'image' => $uploadPath,
                'user_id' => $user_id,
            ]);

            if ($request->hasFile('multi_images')) {
                foreach ($request->file('multi_images') as $file) {
                    $multiImageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $img = $manager->read($file);
                    $img->resize(388, 472)->save(base_path('public/upload/food/multiple/' . $multiImageName));

                    FoodImage::create([
                        'food_id' => $food->id,
                        'image' => $multiImageName,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Food created successfully.',
                'data' => $food,
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Food creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    private function getCoordinatesFromAddress($address)
    {
        $client = new Client();
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $apiKey;

        try {
            $response = $client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            if ($data['status'] === 'OK') {
                $location = $data['results'][0]['geometry']['location'];
                return [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                ];
            }

            return null;

        } catch (Exception $e) {
            return null;
        }
    }
    public function DetailsPage(Request $request)
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

        return view('backend.pages.food.food-details');
    }

    public function show($id)
    {
        try {
            $food = Food::with('user', 'foodImages')->find($id);

            if (!$food) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Food not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $food
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function EditPage(){
        return view('backend.pages.food.edit');
    }


    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'gradients' => 'required|string',
                'expire_date' => 'required|date',
                'description' => 'required|string|min:20',
                'collection_date' => 'required|date',
                'start_collection_time' => 'required',
                'end_collection_time' => 'required',
                'address' => 'required|string|min:3|max:255',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user_id = $request->header('id');
            $food_id = $request->input('id');
            $food = Food::findOrFail($food_id);

            $address = $request->input('address');
            $geoData = $this->getCoordinatesFromAddress($address);

            if (!$geoData) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Unable to fetch coordinates for the provided address.'
                ], 400);
            }

            $latitude = $geoData['latitude'];
            $longitude = $geoData['longitude'];

            if ($request->hasFile('image')) {
                $large_image_path = base_path('public/upload/food/large/');
                $medium_image_path = base_path('public/upload/food/medium/');
                $small_image_path = base_path('public/upload/food/small/');

                if (!empty($food->image)) {
                    if (file_exists($large_image_path . $food->image)) {
                        unlink($large_image_path . $food->image);
                    }
                    if (file_exists($medium_image_path . $food->image)) {
                        unlink($medium_image_path . $food->image);
                    }
                    if (file_exists($small_image_path . $food->image)) {
                        unlink($small_image_path . $food->image);
                    }
                }

                $image = $request->file('image');
                $manager = new ImageManager(new Driver());
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $img = $manager->read($image);

                $img->resize(570, 380)->save($large_image_path . $imageName);
                $img->resize(388, 472)->save($medium_image_path . $imageName);
                $img->resize(346, 415)->save($small_image_path . $imageName);

                $uploadPath = $imageName;
            } else {
                $uploadPath = $food->image;
            }

            $gradientsArray = json_decode($request->input('gradients'), true);
            $gradientsString = implode(',', array_column($gradientsArray, 'value'));
            
            $food->update([
                'name' => $request->input('name'),
                'gradients' => $gradientsString,
                'expire_date' => $request->input('expire_date'),
                'description' => $request->input('description'),
                'collection_date' => $request->input('collection_date'),
                'start_collection_time' => $request->input('start_collection_time'),
                'end_collection_time' => $request->input('end_collection_time'),
                'address' => $request->input('address'),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'image' => $uploadPath
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Food updated successfully.',
                'data' => $food
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Food update failed',
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
        $food_id = $request->input('id');

        DB::beginTransaction();
        try {
            $food = Food::findOrFail($food_id);

            $large_image_path = base_path('public/upload/food/large/');
            $medium_image_path = base_path('public/upload/food/medium/');
            $small_image_path = base_path('public/upload/food/small/');
            $multiple_image_path = base_path('public/upload/food/multiple/');

            foreach ($food->foodImages as $foodImage) {
                if (File::exists($multiple_image_path . $foodImage->image)) {
                    File::delete($multiple_image_path . $foodImage->image);
                }
                $foodImage->delete();
            }

            foreach (['large', 'medium', 'small'] as $size) {
                $imagePath = ${$size . '_image_path'} . $food->image;
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }

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

            if ($food->order) {
                $food->order->delete();
            }

            $food->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Food and related images deleted successfully.'
            ], 200);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Food not found',
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


    public function FoodPublish(Request $request)
    {
        try {
            $food_id = $request->input('id');
            $food = Food::with('user')->findOrFail($food_id);
            
            $result = $food->update([
                'status' => 'published'
            ]);

            if ($food->user->role === 'client') {
                $food->user->notify(new FoodPublishNotification($food));
            }

           $customers = User::where('role', 'user')->get();
            foreach ($customers as $customer) {
                $customer->notify(new FoodPublishNotification($food));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Food published successfully and notifications sent.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Food not found',
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

}