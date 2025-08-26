<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfilePhotoUrlController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'avatar_url' => ['required', 'url', 'max:2000'], // validación básica
        ]);

        $request->user()->forceFill([
            'profile_photo_path' => $data['avatar_url'],
        ])->save();

        return back()->with('status', 'photo-url-updated');
    }
}