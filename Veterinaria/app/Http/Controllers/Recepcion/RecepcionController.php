<?php

namespace App\Http\Controllers\Recepcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class RecepcionController extends Controller
{
    public function home()
    {
        return view('recepcion.home');
    }

    public function medicos()
    {
        return view('recepcion.medicos');
    }

    public function expedientes()
    {
        return view('recepcion.expedientes');
    }

    public function recetas()
    {
        return view('recepcion.recetas');
    }

    public function honorarios()
    {
        return view('recepcion.honorarios');
    }

    public function hospitalizaciones()
    {
        return view('recepcion.hospitalizaciones');
    }
}

