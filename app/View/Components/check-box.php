<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CheckBox extends Component
{
    public string $name;
    public $value;
    public string $label;
    public bool $checked;
    public ?string $id;

    /**
     * Create a new component instance.
     *
     * @param string $name
     * @param mixed $value
     * @param string $label
     * @param bool|mixed $checked
     * @param string|null $id
     */
    public function __construct($name = 'option[]', $value = '', $label = '', $checked = false, $id = null)
    {
        $this->name    = $name;
        $this->value   = $value;
        $this->label   = $label;
        $this->checked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
        $this->id      = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.check-box', ['id' => $this->id]);
    }
}
