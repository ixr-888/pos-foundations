<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'customerCount' => Customer::query()->count(),
            'userCount' => User::query()->count(),
            'activeUserCount' => User::query()->where('status', 'Active')->count(),
            'recentCustomers' => Customer::query()
                ->orderByDesc('joined_on')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about');
    }
}
