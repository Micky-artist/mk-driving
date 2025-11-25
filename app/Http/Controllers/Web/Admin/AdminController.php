<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard(): View
    {
        return view('admin.dashboard');
    }

    /**
     * Display the admin settings page.
     *
     * @return \Illuminate\View\View
     */
    public function settings(): View
    {
        return view('admin.settings');
    }

    /**
     * Update the admin settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        // Add your settings validation and update logic here
        
        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully.');
    }
}
