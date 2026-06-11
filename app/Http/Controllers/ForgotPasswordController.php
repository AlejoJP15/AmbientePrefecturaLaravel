<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('recuperar.solicitar');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email'    => 'Formato de correo inválido.',
        ]);

        $status = Password::broker('personas')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Hemos enviado el enlace de recuperación a tu correo.')
            : back()->withErrors(['email' => 'No pudimos enviar el enlace a ese correo.']);
    }
}
