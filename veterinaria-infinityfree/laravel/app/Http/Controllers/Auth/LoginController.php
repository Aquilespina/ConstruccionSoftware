<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->tipo_permiso);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo_electronico' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = (bool) $request->boolean('remember');

        // Añadimos estado = 1 para solo permitir usuarios activos
        $attempt = Auth::attempt([
            'correo_electronico' => $credentials['correo_electronico'],
            'password' => $credentials['password'],
            'estado' => 1,
        ], $remember);

        if ($attempt) {
            $request->session()->regenerate();
            $role = Auth::user()->tipo_permiso;
            return $this->redirectByRole($role);
        }

        return back()->withErrors([
            'correo_electronico' => 'Credenciales inválidas o usuario inactivo.',
        ])->onlyInput('correo_electronico');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(?string $role)
    {
        return match ($role) {
            'administrador' => redirect()->route('admin.home'),
            'medico' => redirect()->route('medico.home'),
            'recepcionista' => redirect()->route('recepcion.home'),
            default => redirect()->route('login'),
        };
    }
}
