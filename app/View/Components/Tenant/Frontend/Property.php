<?php

namespace App\View\Components\Tenant\Frontend;

use Illuminate\View\Component;

class Property extends Component
{
    public $property;

    public  $animation;

    protected $except = [];
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($property, $animate = true)
    {

        $this->property = $property;
        $this->animation = $animate;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.tenant.frontend.property');
    }
}
