<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'gender' => ['required', 'in:male,female'],
            'age' => ['required', 'integer', 'min:10', 'max:100'],
            'weight_kg' => ['required', 'numeric', 'min:30', 'max:300'],
            'height_cm' => ['required', 'numeric', 'min:100', 'max:250'],
            'activity_level' => ['required', 'in:sedentary,lightly_active,moderately_active,very_active'],
            'diet_goal' => ['required', 'in:weight_loss,maintenance,weight_gain'],
            'allergies' => ['nullable', 'string', 'max:255'],
        ]);

        $weight = $request->weight_kg;
        $height = $request->height_cm;
        $age = $request->age;

        // Hitung BMR & TDEE (Rumus Harris-Benedict)
        if ($request->gender === 'male') {
            $bmr = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
        } else {
            $bmr = 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
        }

        $activityMultipliers = [
            'sedentary' => 1.2,
            'lightly_active' => 1.375,
            'moderately_active' => 1.55,
            'very_active' => 1.725,
        ];
        $tdee = $bmr * $activityMultipliers[$request->activity_level];

        if ($request->diet_goal === 'weight_loss') {
            $dailyCalorieTarget = $tdee - 500;
        } elseif ($request->diet_goal === 'weight_gain') {
            $dailyCalorieTarget = $tdee + 500;
        } else {
            $dailyCalorieTarget = $tdee;
        }

        // 3. Simpan ke database
        UserProfile::create([
            'user_id' => Auth::id(),
            'gender' => $request->gender,
            'age' => $request->age,
            'weight_kg' => $request->weight_kg,
            'height_cm' => $request->height_cm,
            'activity_level' => $request->activity_level,
            'diet_goal' => $request->diet_goal,
            'daily_calorie_target' => round($dailyCalorieTarget),
            'allergies' => $request->allergies,
        ]);

        return redirect()->back()->with('success', 'Data profil kesehatan berhasil disimpan!');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
