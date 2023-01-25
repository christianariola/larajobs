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
            'listings' => Listing::latest()->filter(request(['tag', 'search']))->paginate(6),
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
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => ['required', 'email', Rule::unique('listings', 'email')],
            'description' => 'required',
            'tags' => 'required',
        ]);

        // $listing = new Listing();
        // $listing->title = $request->title;
        // $listing->description = $request->description;
        // $listing->tags = $request->tags;
        // $listing->save();

        if($request->hasFile('logo')){
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $formFields['user_id'] = auth()->id();

        Listing::create($formFields);

        return redirect('/')->with('message', 'Listing created successfully!');
    }

    public function edit(Listing $listing) {
        return view('listings.edit', [
            'listing' => $listing,
        ]);
    }

    public function update(Request $request, Listing $listing) {

        // Check if logged in user is the owner of the listing
        if($listing->user_id !== auth()->id()){
            return back()->with('message', 'You are not authorized to edit this listing.');
        }

        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required'],
            'location' => 'required',
            'website' => 'required',
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => ['required', 'email'],
            'description' => 'required',
            'tags' => 'required',
        ]);

        if($request->hasFile('logo')){
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $listing->update($formFields);

        return back()->with('message', 'Listing updated successfully!');
    }

    public function destroy(Listing $listing) {
        // Check if logged in user is the owner of the listing
        if($listing->user_id !== auth()->id()){
            return back()->with('message', 'You are not authorized to edit this listing.');
        }

        $listing->delete();

        return redirect('/')->with('message', 'Listing deleted successfully!');
    }

    public function manageListings() {
        return view('listings.manage', [
            'listings' => Listing::where('user_id', auth()->id())->latest()->paginate(6),
            // 'listings' => auth()->user()->listings()->latest()->paginate(6),
        ]);
    }
}
