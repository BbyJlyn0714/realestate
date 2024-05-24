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
}
