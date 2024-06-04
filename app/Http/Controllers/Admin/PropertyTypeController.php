<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    public function AllType()
    {
        $types = PropertyType::latest()->get();

        return view('admin.property.all_type', compact('types'));

    } // End Method

    public function create()
    {
        return view('admin.property.add_type');
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'type_name' => 'required|unique:property_types|max:200',
            'type_icon' => 'required',
        ]);

        PropertyType::insert([
            'type_name' => $request->type_name,
            'type_icon' => $request->type_icon,
        ]);

        $notification = [
            'message' => 'Property Type Create Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('property.all.type')->with($notification);
    }

    public function edit($id)
    {
        $types = PropertyType::findOrFail($id);

        return view('admin.property.edit_type', compact('types'));
    }

    public function update(Request $request)
    {
        $pid = $request->id;

        PropertyType::findOrFail($pid)->update([
            'type_name' => $request->type_name,
            'type_icon' => $request->type_icon,
        ]);

        $notification = [
            'message' => 'Property Type Updated Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('property.all.type')->with($notification);
    }

    public function delete($id)
    {

        PropertyType::findOrFail($id)->delete();

        $notification = [
            'message' => 'Property Type Deleted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);

    }// End Method
}
