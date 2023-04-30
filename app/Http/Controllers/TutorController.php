<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tutor;



class TutorController extends Controller
{
    
    public function index()
    {
        return Inertia::render('Tutores/index');
    }


}
