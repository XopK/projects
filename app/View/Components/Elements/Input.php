<?php

namespace App\View\Components\Elements;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public $label;
    public $name;
    public $type;
    public $id;
    public $value;
    public $placeholder;
    public $min;
    public $max;
    public $isRequired;
    public $class;
    public $optional;

    public function __construct($label, $name, $type = 'text', $id = null, $value = null, $placeholder = null, $isRequired = false, $min = 0, $max = null, $class = null, $optional = null)
    {
        $this->label = $label;
        $this->name = $name;
        $this->type = $type;
        $this->id = $id;
        $this->placeholder = $placeholder;
        $this->min = $min;
        $this->max = $max;
        $this->value = $value;
        $this->isRequired = $isRequired;
        $this->class = $class;
        $this->optional = $optional;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.elements.input');
    }
}
