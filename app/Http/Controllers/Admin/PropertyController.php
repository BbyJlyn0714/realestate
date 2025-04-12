<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenities;
use App\Models\Facility;
use App\Models\MultiImage;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::latest()->get();

        return view('admin.property.property_index', compact('properties'));
    }

    public function create()
    {
        $propertytype = PropertyType::latest()->get();
        $amenities = Amenities::latest()->get();
        $activeAgent = User::where('status', 'active')->where('role', 'agent')->latest()->get();

        return view('admin.property.property_add', compact('propertytype', 'amenities', 'activeAgent'));
    }

    public function store(Request $request)
    {
        \DB::beginTransaction();

        try {
            $amen = $request->amenities_id;
            $amenities = implode(',', $amen);

            $pcode = IdGenerator::generate(['table' => 'properties', 'field' => 'property_code', 'length' => 5, 'prefix' => 'PC']);

            $image = Image::read($request->file('property_thumbnail'));

            $name_gen = hexdec(uniqid()).'.'.$request->file('property_thumbnail')->getClientOriginalExtension();

            $image->resize(370, 250)->save('upload/images/property/thumbnail/'.$name_gen);
            $save_url = 'upload/images/property/thumbnail/'.$name_gen;

            $property_id = Property::insertGetId([
                'property_type_id' => $request->ptype_id,
                'amenities_id' => $amenities,
                'property_name' => $request->property_name,
                'property_slug' => strtolower(str_replace(' ', '-', $request->property_name)),
                'property_code' => $pcode,
                'property_status' => $request->property_status,
                'lowest_price' => $request->lowest_price,
                'max_price' => $request->max_price,
                'short_description' => $request->short_descp,
                'long_description' => $request->long_descp,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'garage' => $request->garage,
                'garage_size' => $request->garage_size,
                'property_size' => $request->property_size,
                'property_video' => $request->property_video,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'neighborhood' => $request->neighborhood,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'featured' => $request->featured,
                'hot' => $request->hot,
                'agent_id' => $request->agent_id,
                'status' => 1,
                'property_thumbnail' => $save_url,
                'created_at' => Carbon::now(),
            ]);

            foreach ($request->file('multi_img') as $images) {
                $img = Image::read($images);
                $make_me = hexdec(uniqid()).'.'.$images->getClientOriginalExtension();
                $img->resize(770, 520)->save('upload/images/property/multi-image/'.$make_me);
                $uploadPath = 'upload/images/property/multi-image/'.$make_me;

                MultiImage::insert([
                    'property_id' => $property_id,
                    'photo_name' => $uploadPath,
                    'created_at' => Carbon::now(),
                ]);
            }

            $facilities = count($request->facility_name);

            if ($facilities != null) {
                for ($i = 0; $i < $facilities; $i++) {
                    $fcount = new Facility();
                    $fcount->property_id = $property_id;
                    $fcount->facility_name = $request->facility_name[$i];
                    $fcount->distance = $request->distance[$i];
                    $fcount->save();
                }
            }

            \DB::commit();

            $notification = [
                'message' => 'Property Inserted Successfully',
                'alert-type' => 'success',
            ];

            return redirect()->route('property.index')->with($notification);
        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Error inserting property: '.$e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Failed to insert property. Please try again later.']);
        }

    }

    public function edit(Property $property)
    {
        $property_amenities = explode(',', $property->amenities_id);

        $propertytype = PropertyType::latest()->get();

        $amenities = Amenities::latest()->get();

        $facilities = Facility::where('property_id', $property->id)->get();

        $multiImage = MultiImage::where('property_id', $property->id)->get();

        $activeAgent = User::where('status', 'active')->where('role', 'agent')->latest()->get();

        return view('admin.property.property_edit', compact('property', 'propertytype', 'amenities', 'activeAgent', 'property_amenities', 'multiImage', 'facilities'));
    }


    public function update(Request $request) {

        try {
            $amenities = implode(',', $request->amenities_id);
            Property::findOrFail($request->id)->update([
                'property_type_id' => $request->property_type_id,
                'amenities_id' => $amenities,
                'property_name' => $request->property_name,
                'property_slug' => strtolower(str_replace(' ', '-', $request->property_name)), 
                'property_status' => $request->property_status,

                'lowest_price' => $request->lowest_price,
                'max_price' => $request->max_price,
                'short_description' => $request->short_description,
                'long_description' => $request->long_description,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'garage' => $request->garage,
                'garage_size' => $request->garage_size,

                'property_size' => $request->property_size,
                'property_video' => $request->property_video,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,

                'neighborhood' => $request->neighborhood,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'featured' => $request->featured,
                'hot' => $request->hot,
                'agent_id' => $request->agent_id, 
                'updated_at' => Carbon::now(), 
            ]);

            $notification = array(
                'message' => 'Property Updated Successfully',
                'alert-type' => 'success'
            );
        } catch (QueryException $e) {
            \Log::error('Database error during property update: ' . $e->getMessage());
            $notification = array(
                'message' => 'Failed to update property. Database error.',
                'alert-type' => 'error'
            );
        } catch (Exception $e) {
            \Log::error('Error during property update: ' . $e->getMessage());
            $notification = array(
                'message' => 'An unexpected error occurred while updating the property.',
                'alert-type' => 'error'
            );
        }

        return redirect()->route('property.index')->with($notification); 
    }

    public function updateThumbnail(Request $request) {
        try {
            // Retrieve the old image path from the request
            $oldImage = $request->old_img;
    
            // Read and process the new image
            $image = Image::read($request->file('property_thumbnail'));
            $name_gen = hexdec(uniqid()) . '.' . $request->file('property_thumbnail')->getClientOriginalExtension();
            $image->resize(370, 250)->save('upload/images/property/thumbnail/' . $name_gen);
            $save_url = 'upload/images/property/thumbnail/' . $name_gen;
    
            // Delete the old image if it exists
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }
    
            // Update the property record with the new thumbnail URL
            Property::findOrFail($request->id)->update([
                'property_thumbnail' => $save_url,
                'updated_at' => Carbon::now(),
            ]);
    
            // Prepare a success notification
            $notification = [
                'message' => 'Property Image Thumbnail Updated Successfully',
                'alert-type' => 'success'
            ];
    
            return redirect()->back()->with($notification);
    
        } catch (Exception $e) {
            // Log the error message
            Log::error('Error updating property thumbnail: ' . $e->getMessage());
    
            // Prepare an error notification
            $notification = [
                'message' => 'Failed to update property image thumbnail',
                'alert-type' => 'error'
            ];
    
            return redirect()->back()->with($notification);
        }
    }

    public function updateMultiImage(Request $request) {
        $multiImages = $request->file('multi_img');

        // Check if $multiImages is empty
        if (empty($multiImages)) {
            // Image is empty or not provided, return with error message
            $notification = [
                'message' => 'Image cannot be empty',
                'alert-type' => 'error'
            ];

            return redirect()->back()->with($notification);
        }

        foreach ($multiImages as $id => $img) {
             // Find the multi-image record by ID
            $imgDel = MultiImage::findOrFail($id);
            //  // Delete the old image file
            unlink($imgDel->photo_name);

            // Generate a unique filename for the image
            $make_name = hexdec(uniqid()) . '.' . $img->getClientOriginalExtension();

            // Read and process the new image
            $image = Image::read($img);
            $image->resize(370, 250)->save('upload/images/property/multi-image/' . $make_name);

            // Define the upload path for the new image
            $uploadPath = 'upload/images/property/multi-image/' . $make_name;

             // Update the multi-image record with the new image path and timestamp
            MultiImage::where('id', $id)->update([
                'photo_name' => $uploadPath,
                'updated_at' => Carbon::now(),
            ]);
        }

        // Set the success notification
        $notification = [
            'message' => 'Property Multi Image Updated Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    public function deleteMultiImage(MultiImage $img) {
        unlink($img->photo_name);

        $img->delete();

        $notification = array(
            'message' => 'Property Multi Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 
    }

    public function storeMultiImage(Request $request) {
        $new_multi = $request->imageid;
        $img = $request->file('multi_img');
        if (is_null($img)) {
            $notification = array(
                'message' => 'Please Choose and Image',
                'alert-type' => 'error'
            );
    
            return redirect()->back()->with($notification); 
        }
        $make_name = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
        // Read and process the new image
        $image = Image::read($img);
        $image->resize(770, 520)->save('upload/images/property/multi-image/' . $make_name);
        $uploadPath = 'upload/images/property/multi-image/'.$make_name;

        MultiImage::insert([
            'property_id' => $new_multi,
            'photo_name' => $uploadPath,
            'created_at' => Carbon::now(), 
        ]);

        $notification = array(
            'message' => 'Property Multi Image Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 
    }

    public function updatePropertyFacilities(Request $request) {
        $pid = $request->id;

        if ($request->facility_name == NULL) {
           return redirect()->back();
        }else{
            Facility::where('property_id', $pid)->delete();

          $facilities = Count($request->facility_name); 

           for ($i=0; $i < $facilities; $i++) { 
               $fcount = new Facility();
               $fcount->property_id = $pid;
               $fcount->facility_name = $request->facility_name[$i];
               $fcount->distance = $request->distance[$i];
               $fcount->save();
           } // end for 
        }

         $notification = array(
            'message' => 'Property Facility Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 
    }

    public function delete(Property $property) {
        try {
            unlink($property->property_thumbnail);

            $image = MultiImage::where('property_id', $property->id)->get();

            foreach($image as $img){
                unlink($img->photo_name);
            }

            MultiImage::where('property_id', $property->id)->delete();
            Facility::where('property_id', $property->id)->delete();

            $property->delete();
            
            $notification = array(
                'message' => 'Property Deleted Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->back()->with($notification); 

        } catch (\Exception $e) {
            \Log::info("Database error");
        }
    }

    public function detailsProperty(Property $property) 
    {
        $facilities = Facility::where('property_id',$property->id)->get();

        $type = $property->amenities_id;
        $property_ami = explode(',', $type);

        $multiImage = MultiImage::where('property_id', $property->id)->get();

        $propertytype = PropertyType::latest()->get();
        $amenities = Amenities::latest()->get();
        $activeAgent = User::where('status','active')->where('role','agent')->latest()->get();

        return view('admin.property.property_details',compact('property','propertytype','amenities','activeAgent','property_ami','multiImage','facilities'));
    }

    public function inactiveProperty(Property $property) {
        $property->update([
            'status' => 0,
        ]);

        $notification = array(
            'message' => 'Property Inactive Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('property.index')->with($notification); 
    }

    public function activeProperty(Property $property) {
        $property->update([
            'status' => 1,
        ]);

        $notification = array(
            'message' => 'Property Active Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('property.index')->with($notification); 
    }
}
