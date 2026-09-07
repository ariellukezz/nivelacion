<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request)
    {
        return parent::version($request);
    }

    public function share(Request $request)
    {
        return array_merge(parent::share($request), [

            'auth_user' => function () use ($request) {

                if (!$request->user()) {
                    return null;
                }

                return DB::table('users AS u')
                    ->leftJoin('programa AS p', 'p.id', '=', 'u.programa_id')
                    ->leftJoin('rol AS r', 'r.id', '=', 'u.rol')
                    ->where('u.id', $request->user()->id)
                    ->select(
                        'u.id',
                        'u.nombres',
                        'u.apellidos',
                        'u.email',
                        'u.rol',
                        'u.estado_contraseña AS e_contra',
                        'p.programa',
                        'p.escuela',
                        'r.nombre AS nombre_rol'
                    )
                    ->first();
            },

            'flash' => [
                'status' => fn () => $request->session()->get('status'),
                'found' => fn () => $request->session()->get('found'),
                'email_masked' => fn () => $request->session()->get('email_masked'),
                'lookup_token' => fn () => $request->session()->get('lookup_token'),
            ],

        ]);
    }
}
