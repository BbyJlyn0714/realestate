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
        DB::beginTransaction();

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

            DB::commit();

            $notification = [
                'message' => 'Property Inserted Successfully',
                'alert-type' => 'success',
            ];

            return redirect()->route('property.index')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error inserting property: '.$e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Failed to insert property. Please try again later.']);
        }

    }

    public function edit(Property $property)
    {
        $propertytype = PropertyType::latest()->get();

        $amenities = Amenities::latest()->get();

        $activeAgent = User::where('status', 'active')->where('role', 'agent')->latest()->get();

        return view('admin.property.property_edit', compact('property', 'propertytype', 'amenities', 'activeAgent'));
    }
}
