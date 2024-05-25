<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\MultiImage;
use App\Models\Facility;

class PropertyController extends Controller
{
    public function index() {
        $properties = Property::latest()->get();
        return view('admin.property.property_index', compact('properties'));
    }
}
