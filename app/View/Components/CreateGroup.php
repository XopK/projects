<?php

namespace App\View\Components;

use App\Models\AddressList;
use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CreateGroup extends Component
{
    /**
     * Create a new component instance.
     */

    public $categories;
    public $addresses;

    public function __construct()
    {
        $this->categories = Category::orderBy('name', 'asc')->get();
        $this->addresses = AddressList::orderBy('created_at', 'desc')->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.create-group');
    }
}
