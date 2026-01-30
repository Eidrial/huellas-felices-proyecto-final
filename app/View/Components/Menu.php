<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class Menu extends Component
{
    //usuario actual
    public $user;

    public function __construct()
    {
        //obtener el usuario logueado para que muestre las opciones del menu segun el rol
        $this->user = Auth::user();
    }

    public function render()
    {
        return view('components.menu');
    }
}