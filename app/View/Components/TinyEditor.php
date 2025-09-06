<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TinyEditor extends Component
{
    public $id;
    public $name;
    public $value;
    public $menubar;
    public $label;


    public function __construct($id = 'tiny', $name = 'message', $value = '', $menubar = false , $label)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
        $this->menubar = filter_var($menubar, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tiny-editor');
    }
}
