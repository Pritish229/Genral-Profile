<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class textareabox extends Component
{
    public $label;
    public $placeholder;
    public $name ;
    public $value ;
    public $id ;
    public $helpertxt ;
    
    public function __construct($label , $placeholder , $id ,$name , $value = null , $helpertxt = null)
    {
        //
        $this->label = $label;
        $this->placeholder = $placeholder ;
        $this->name = $name;
        $this->value = $value;
        $this->id = $id;
        $this->helpertxt = $helpertxt;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.textareabox');
    }
}
