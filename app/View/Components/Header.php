<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * Create a new component instance.
     */

    public $notifications;
    public $countNotifications;

    public function __construct()
    {
        $this->notifications = auth()->user()?->unreadNotifications()->take(3)->get();
        $this->countNotifications = auth()->check()
            ? auth()->user()->unreadNotifications()->count()
            : 0;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.header');
    }
}
