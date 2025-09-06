<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RadioButton extends Component
{
    public $name;
    public $value;
    public $label;
    public $checked;

    public function __construct($name, $value, $label, $checked = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
        $this->checked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.radio-button');
    }
}
