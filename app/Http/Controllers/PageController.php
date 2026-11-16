<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'taskCount' => Task::active()->count(),
            'openCount' => Task::active()->where('status', '!=', 'done')->count(),
            'doneCount' => Task::active()->where('status', 'done')->count(),
            'todayCount' => Task::active()->whereDate('due_date', today())->count(),
            'upcomingTasks' => Task::active()->where('status', '!=', 'done')
                ->orderBy('due_date')->orderBy('id')->limit(4)->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function profile(): View
    {
        return view('pages.profile');
    }
}
