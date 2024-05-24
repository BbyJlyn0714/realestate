<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amenities;

class AmenitiesController extends Controller
{
    public function index() {
        $amenities = Amenities::latest()->get();
        return view('admin.amenities.index', compact('amenities'));
    }

    public function create() {
        return view('admin.amenities.add');
    }

    public function store(Request $request) {
        Amenities::insert([ 
            'amenities_name' => $request->amenities_name, 
        ]);

          $notification = array(
            'message' => 'Amenities Create Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('amenities.index')->with($notification);
    }

    public function edit($id) {
        $amenities = Amenities::findOrFail($id);
        return view('admin.amenities.edit', compact('amenities'));
    }

    public function update(Request $request) {
        $ame_id = $request->id;

        Amenities::findOrFail($ame_id)->update([ 
            'amenities_name' => $request->amenities_name, 
        ]);

          $notification = array(
            'message' => 'Amenities Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('amenities.index')->with($notification);
    }

    public function delete($id) {
        Amenities::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Amenities Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}
