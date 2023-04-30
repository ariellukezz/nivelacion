<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Alumno;

class AlumnoController extends Controller
{
    public function index()
    {
        return Inertia::render('Alumno/index');
    }
}
