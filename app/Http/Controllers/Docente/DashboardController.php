<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tutor;
use App\Models\Docente;
use App\Models\CursoDetalle;
use App\Models\Curso;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index()
    {
        return Inertia::render('Docente/Dashboard/index');
    }

}
