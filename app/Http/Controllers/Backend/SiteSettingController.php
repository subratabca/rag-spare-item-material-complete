<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\SiteSetting;

class SiteSettingController extends Controller
{
    function SettingPage()
    {
        return view('backend.pages.site-setting.index');
    }


    public function index(Request $request)
    {
        try {
            $siteSetting = SiteSetting::latest()->get();

            return response()->json([
                'status' => 'success',
                'data' => $siteSetting
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
        return view('backend.pages.site-setting.create');
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|min:3|max:50',
                'email' => 'required|email|max:50|unique:site_settings,email', 
                'phone1' => 'required|string|min:10|max:15', 
                'phone2' => 'nullable|string|min:10|max:15', 

                'address' => 'required|string|min:3|max:50',
                'city' => 'required|string|min:3|max:50', 
                'country' => 'required|string|min:3|max:50', 
                'zip_code' => 'required|string|min:3|max:10', 

                'facebook' => 'nullable|url|max:50', 
                'linkedin' => 'nullable|url|max:50', 
                'youtube' => 'nullable|url|max:50', 

                'description' => 'required|string|min:20',
                'refund' => 'required|string|min:20',
                'terms' => 'required|string|min:20',
                'privacy' => 'required|string|min:20',

                'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
            ]);


            $user_id = $request->header('id');

            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $manager = new ImageManager(new Driver());
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $img = $manager->read($image);

                $img->resize(100,50)->save(base_path('public/upload/site-setting/'.$imageName));
                $uploadPath = $imageName;
            }else{
                $uploadPath = null; 
            }

            $settingData = SiteSetting::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone1' => $request->input('phone1'),
                'phone2' => $request->input('phone2'),
                'logo' => $uploadPath,

                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'country' => $request->input('country'),
                'zip_code' => $request->input('zip_code'),

                'facebook' => $request->input('facebook'),
                'linkedin' => $request->input('linkedin'),
                'youtube' => $request->input('youtube'),

                'description' => $request->input('description'),
                'refund' => $request->input('refund'),
                'terms' => $request->input('terms'),
                'privacy' => $request->input('privacy'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Setting created successfully.',
                'data' => $settingData,
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
                'message' => 'Food creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $setting = SiteSetting::find($id);

            if (!$setting) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Setting info not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $setting
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
        return view('backend.pages.site-setting.edit');
    }


    public function update(Request $request)
    {
        try {
            $id = $request->input('id');

            $request->validate([
                'name' => 'required|string|min:3|max:50',
               'email' => 'required|email|max:50|unique:site_settings,email,' . $id,
                'phone1' => 'required|string|min:10|max:15', 
                'phone2' => 'nullable|string|min:10|max:15', 

                'address' => 'required|string|min:3|max:50',
                'city' => 'required|string|min:3|max:50', 
                'country' => 'required|string|min:3|max:50', 
                'zip_code' => 'required|string|min:3|max:10', 

                'facebook' => 'nullable|url|max:50', 
                'linkedin' => 'nullable|url|max:50', 
                'youtube' => 'nullable|url|max:50', 

                'description' => 'required|string|min:20',
                'refund' => 'required|string|min:20',
                'terms' => 'required|string|min:20',
                'privacy' => 'required|string|min:20',

                'logo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048', 
            ]);

            $siteSetting = SiteSetting::findOrFail($id);

            if ($request->hasFile('logo')) {
                $image_path = base_path('public/upload/site-setting/');

                if(!empty($siteSetting->logo)){
                    if(file_exists($image_path.$siteSetting->logo)){
                        unlink($image_path.$siteSetting->logo);
                    }
                }

                $image = $request->file('logo');
                $manager = new ImageManager(new Driver());
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $img = $manager->read($image);

                $img->resize(100,50)->save($image_path.$imageName);

                $uploadPath = $imageName;
            }else{
                $uploadPath = $siteSetting->logo;
            }

            
            $siteSetting->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone1' => $request->input('phone1'),
                'phone2' => $request->input('phone2'),
                'logo' => $uploadPath,

                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'country' => $request->input('country'),
                'zip_code' => $request->input('zip_code'),

                'facebook' => $request->input('facebook'),
                'linkedin' => $request->input('linkedin'),
                'youtube' => $request->input('youtube'),

                'description' => $request->input('description'),
                'refund' => $request->input('refund'),
                'terms' => $request->input('terms'),
                'privacy' => $request->input('privacy'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Setting information updated successfully.',
                'data' => $siteSetting
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
            $user_id = $request->header('id');
            $setting_id = $request->input('id');

            $siteSetting = SiteSetting::findOrFail($setting_id);
            $image_path = base_path('public/upload/site-setting/');

            if(!empty($siteSetting->logo)){
                if(file_exists($image_path.$siteSetting->logo)){
                    unlink($image_path.$siteSetting->logo);
                }
            }

            $siteSetting->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Site setting and related images deleted successfully..'
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
                'message' => 'Deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
}