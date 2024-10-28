<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\About;

class AboutController extends Controller
{
    public function AboutPage()
    {
        return view('backend.pages.about.index');
    }


    public function index(Request $request)
    {
        try {
            $about = About::latest()->get();

            return response()->json([
                'status' => 'success',
                'data' => $about
            ], 200); 

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while retrieving data',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function create()
    {
        return view('backend.pages.about.create');
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|min:3|max:50',
                'description' => 'required|string|min:10',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $manager = new ImageManager(new Driver());
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $img = $manager->read($image);

                $img->resize(400,500)->save(base_path('public/upload/about/'.$imageName));
                $uploadPath = $imageName;
            }else{
                $uploadPath = null; 
            }

            $aboutData = About::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'image' => $uploadPath,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'About information created successfully.',
                'data' => $aboutData,
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
                'message' => 'About information creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function show($id)
    {
        try {
            $about = About::find($id);

            if (!$about) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'About info not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $about
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function EditPage()
    {
        return view('backend.pages.about.edit');
    }


    public function update(Request $request)
    {
        try {
            $id = $request->input('id');

            $request->validate([
                'title' => 'required|string|min:3|max:50', 
                'description' => 'required|string|min:10',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048', 
            ]);

            $about = About::findOrFail($id);

            if ($request->hasFile('image')) {
                $image_path = base_path('public/upload/about/');

                if(!empty($about->image)){
                    if(file_exists($image_path.$about->image)){
                        unlink($image_path.$about->image);
                    }
                }

                $image = $request->file('image');
                $manager = new ImageManager(new Driver());
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $img = $manager->read($image);

                $img->resize(400,500)->save($image_path.$imageName);

                $uploadPath = $imageName;
            }else{
                $uploadPath = $about->image;
            }

            
            $about->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'image' => $uploadPath,


            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Setting information updated successfully.',
                'data' => $about
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


    public function delete(Request $request)
    {
        try {
            $about_id = $request->input('id');
            $about = About::findOrFail($about_id);
            $image_path = base_path('public/upload/about/');

            if(!empty($about->image)){
                if(file_exists($image_path.$about->image)){
                    unlink($image_path.$about->image);
                }
            }

            $about->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'About information and related images deleted successfully..'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'About information not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}