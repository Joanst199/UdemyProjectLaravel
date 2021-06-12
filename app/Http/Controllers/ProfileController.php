<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    function edit(Request $request)
    {
        return view('profiles.edit')->with([
            'user'=>$request->user()
        ]);
    }

    function update(ProfileRequest $request)
    {
        return DB::transaction(function () use($request)
        {
            $user = $request->user();

            $user->fill($request->validated());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
                $user->sendEmailVerificationNotification();
            }
            $user->save();

            if ($request->hasFile('image')) {
                if ($user->image != null) {
                    Storage::disk('images')->delete($user->image->path);
                    $user->image->delete();
                }

                $user->image()->create([
                    'path' => $request->image->store('users', 'images'),
                ]);
            }

            return redirect()
                ->route('profile.edit')
                ->withSuccess('Profile edited');
        },5);
    }


}
