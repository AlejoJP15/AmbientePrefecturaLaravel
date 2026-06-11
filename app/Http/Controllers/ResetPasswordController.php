<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function showResetForm(string $token, Request $request)
    {
         return view('recuperar.restablecer', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required','email'],
            // Reglas de contraseña segura:
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                // Al menos 1 mayúscula, 1 número y 1 carácter especial:
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/'
            ],
        ], [
            'password.required'   => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.confirmed'  => 'Las contraseñas no coinciden.',
            'password.regex'      => 'Debe incluir al menos 1 mayúscula, 1 número y 1 carácter especial.',
        ]);

        $status = Password::broker('personas')->reset(
            $request->only('email','password','password_confirmation','token'),
            function ($persona, $password) {
                // Tu mutator hará el hash automáticamente
                $persona->contraseña = $password;
                $persona->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Tu contraseña fue restablecida correctamente.')
            : back()->withErrors(['email' => 'No se pudo restablecer la contraseña con esos datos.']);
    }
}
