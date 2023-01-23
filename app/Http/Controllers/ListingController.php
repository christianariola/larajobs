<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    // Show all listings
    public function index() {
        return view('listings.index', [
            'heading' => 'Latest Listings',
            'listings' => Listing::latest()->filter(request(['tag', 'search']))->get(),
        ]);
    }

    // Show a single listing
    public function show(Listing $listing) {
        return view('listings.show', [
            'listing' => $listing,
        ]);
    }

    // Show form to create a listing
    public function create() {
        return view('listings.create');
    }

    // Save a new listing
    public function store(Request $request) {
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email', Rule::unique('listings', 'email')],
            'description' => 'required',
            'tags' => 'required',
        ]);

        // $listing = new Listing();
        // $listing->title = $request->title;
        // $listing->description = $request->description;
        // $listing->tags = $request->tags;
        // $listing->save();

        return redirect('/');
    }
}
