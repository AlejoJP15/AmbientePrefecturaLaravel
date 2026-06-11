<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function showLogin()
    {
        return view('persona.login');
    }

    /**
     * Procesa el login del usuario.
     */
    public function login(Request $request)
    {
        // Validar campos del formulario
        $credentials = $request->validate([
            'usuario' => ['required'],
            'password' => ['required'],
        ]);

        // Intentar autenticación: usuario ➜ documento_ci
        if (Auth::attempt([
            'usuario' => $request->usuario,
            'password' => $request->password
        ])) {
            $request->session()->regenerate();
            return $this->redirectByRole();
        }

        // Si falla
        return back()->withErrors([
            'usuario' => 'Documento o contraseña incorrectos.',
        ])->onlyInput('usuario');
    }

    /**
     * Redirecciona según el rol del usuario autenticado.
     */
    protected function redirectByRole()
    {
        $user = Auth::user();

        // Obtener el nombre del perfil/rol (ajusta si tu relación se llama distinto)
        $rol = optional($user->perfil)->descripcion;

        switch ($rol) {
            case 'Administrador':
                return redirect()->route('admin.dashboard');
            case 'Técnico':
                return redirect()->route('tecnico.dashboard');
            case 'Coordinador':
                return redirect()->route('coordinador.dashboard');
            case 'Director':
                return redirect()->route('director.dashboard');
            case 'Usuario externo':
                return redirect()->route('usuario.dashboard');
            default:
                Auth::logout();
                return redirect('/login')->withErrors('Rol no válido o sin permisos.');
        }
    }

    /**
     * Cierra la sesión del usuario.
     */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Obtener el nombre de la cookie de sesión de Laravel
        $cookieName = config('session.cookie');
        $forgetCookie = cookie()->forget($cookieName);

        // Devolver el redirect con la cookie eliminada
        return redirect('/login')->withCookie($forgetCookie);
    }
}
