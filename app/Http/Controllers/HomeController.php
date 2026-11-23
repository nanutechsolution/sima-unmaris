<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\FacilityFeedback;
use App\Models\Room;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman publik utama (Landing Page) SIMA UNMARIS.
     * Sumber statistik inventaris disambil dari database agar landing page
     * selalu mencerminkan kondisi data terkini.
     */
    public function index(): View
    {
        $stats = [
            'total_assets' => (int) Asset::count(),
            'inventory_value' => (float) (Asset::sum('acquisition_value') ?? 0),
            'categories' => (int) Category::count(),
            'rooms' => (int) Room::count(),
            'active_surveys' => (int) FacilityFeedback::where('status', 'active')->count(),
        ];

        return view('frontend.home', ['stats' => $stats]);
    }
}
