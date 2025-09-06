<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class inputbox extends Component
{
    public $label;
    public $type;
    public $placeholder;
    public $name;
    public $value;
    public $id;
    public $disabled;
    public $required;
    public $helpertxt;
    public function __construct($label, $type, $placeholder, $name, $id, $value = null, $disabled = false, $helpertxt = null, $required = false)
    {
        //
        $this->label = $label;
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->name = $name;
        $this->value = $value;
        $this->id = $id;
        $this->disabled = $disabled;
        $this->required = $required;
        $this->helpertxt = $helpertxt;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.inputbox');
    }
}
