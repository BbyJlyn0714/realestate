<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenities;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;

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
}
