<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use App\Models\User;
use App\Mail\NewTemporaryPasswordMail;

class RecoveryController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/Recover', [
            'status' => session('status')
        ]);
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:alumno,docente',
            'identificador' => 'required|string|max:50',
        ]);

        if ($request->tipo === 'docente') {
            $row = DB::table('users as u')
                ->join('docente as d', 'd.usuario_id', '=', 'u.id')
                ->join('rol as r', 'r.id', '=', 'u.rol')
                ->where('d.nro_doc', $request->identificador)
                ->select([
                    'u.id as user_id',
                    'u.email as user_email',
                    'u.rol as rol_id',
                    'u.estado_contraseña',
                    'd.nro_doc as docente_dni',
                    'd.email as docente_email',
                    'r.nombre as rol_nombre',
                ])
                ->first();
        } else {
            $row = DB::table('users as u')
                ->join('estudiante as e', 'e.usuario_id', '=', 'u.id')
                ->join('rol as r', 'r.id', '=', 'u.rol')
                ->where('e.codigo_est', $request->identificador)
                ->select([
                    'u.id as user_id',
                    'u.email as user_email',
                    'u.rol as rol_id',
                    'u.estado_contraseña',
                    'e.id as estudiante_id',
                    'e.codigo_est as estudiante_codigo',
                    'e.email as estudiante_email',
                    'r.nombre as rol_nombre',
                ])
                ->first();
        }

        if (!$row) {
            return back()->withErrors(['identificador' => 'No se encontró un usuario con esos datos.']);
        }

        $email = $this->bestEmail(
            $row->user_email ?? null,
            $request->tipo === 'docente' ? ($row->docente_email ?? null) : ($row->estudiante_email ?? null)
        );
        if (!$email) {
            return back()->withErrors(['email' => 'No hay un correo válido registrado para este usuario.']);
        }

        $masked = $this->maskEmail($email);

        $lookupToken = Str::random(40);
        session([
            'recover_lookup_token' => $lookupToken,
            'recover_user_id' => $row->user_id,
            'recover_email' => $email,
        ]);

        return back()->with([
            'found' => true,
            'email_masked' => $masked,
            'lookup_token' => $lookupToken,
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'lookup_token' => 'required|string',
            'modo' => 'required|in:link,password',
        ]);

        if ($request->lookup_token !== session('recover_lookup_token')) {
            return back()->withErrors(['lookup_token' => 'Sesión inválida o expirada. Intenta de nuevo.']);
        }

        $userId = session('recover_user_id');
        $email  = session('recover_email');
        $user   = User::find($userId);

        if (!$user || !$email) {
            return back()->withErrors(['user' => 'Usuario no disponible. Repite la verificación.']);
        }

        if ($request->modo === 'link') {
            $status = Password::sendResetLink(['email' => $email]);

            if ($status === Password::RESET_LINK_SENT) {
                session()->forget(['recover_lookup_token', 'recover_user_id', 'recover_email']);
                return back()->with('status', __($status));
            }
            return back()->withErrors(['email' => __($status)]);
        }

        // Contraseña temporal + forzar cambio
        $tempPassword = Str::random(10);

        $user->password = Hash::make($tempPassword);
        $user->estado_contraseña = 1; // obligará a cambiar al entrar
        $user->save();

        Mail::to($email)->send(new NewTemporaryPasswordMail($user, $tempPassword));

        session()->forget(['recover_lookup_token', 'recover_user_id', 'recover_email']);

        return back()->with('status', 'Se envió una contraseña temporal a tu correo.');
    }

    private function maskEmail(string $email): string
{
    if (strpos($email, '@') === false) {
        return '***';
    }

    [$local, $domain] = explode('@', $email, 2);

    $len = mb_strlen($local);
    // Muestra hasta 4 primeras letras, pero siempre oculta al menos 1 carácter
    $visible = min(4, max(0, $len - 1));
    $masked = mb_substr($local, 0, $visible)
            . str_repeat('*', max(1, $len - $visible));

    return $masked . '@' . $domain;
}

    private function bestEmail(?string $usersEmail, ?string $altEmail): ?string
    {
        $isValid = fn($e) => is_string($e) && strpos($e, '@') !== false;
        if ($isValid($usersEmail)) return $usersEmail;
        if ($isValid($altEmail))   return $altEmail;
        return null;
    }
}
