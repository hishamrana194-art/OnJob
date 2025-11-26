<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Show the form for creating a new profile
     */
    public function create()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Please login first to create a profile.');
        }

        // Check if user already has a profile
        $profile = Auth::user()->profile;

        return view('profile', compact('profile'));
    }

    /**
     * Store a newly created profile in storage
     */
    public function store(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Please login first to create a profile.');
        }

        $validator = Validator::make($request->all(), [
            'frist_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'required|integer|min:18|max:100',
            'company' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'bio' => 'required|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle profile photo upload
        $imageName = null;
        if ($request->hasFile('profile_photo')) {
            $imageName = time() . '.' . $request->profile_photo->extension();
            $request->profile_photo->move(public_path('profiles'), $imageName);
        }

        // Check if user already has a profile
        $existingProfile = Profile::where('user_id', Auth::id())->first();

        if ($existingProfile) {
            // Update existing profile
            if ($imageName && $existingProfile->profile_photo) {
                // Delete old photo
                if (file_exists(public_path('profiles/' . $existingProfile->profile_photo))) {
                    unlink(public_path('profiles/' . $existingProfile->profile_photo));
                }
            }

            $existingProfile->update([
                'frist_name' => $request->frist_name,
                'last_name' => $request->last_name,
                'age' => $request->age,
                'company' => $request->company,
                'job_title' => $request->job_title,
                'country' => $request->country,
                'phone' => $request->phone,
                'address' => $request->address,
                'gender' => $request->gender,
                'bio' => $request->bio,
                'profile_photo' => $imageName ?? $existingProfile->profile_photo,
            ]);

            return redirect()->back()->with('success', 'Profile updated successfully!');
        }

        // Create new profile
        Profile::create([
            'user_id' => Auth::id(),
            'frist_name' => $request->frist_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'company' => $request->company,
            'job_title' => $request->job_title,
            'country' => $request->country,
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender,
            'bio' => $request->bio,
            'profile_photo' => $imageName,
        ]);

        return redirect()->back()->with('success', 'Profile created successfully!');
    }
}

