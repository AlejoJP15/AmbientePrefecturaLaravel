<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as Base;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Base
{
    public function toMail($notifiable)
    {
        // Genera la URL al formulario de reseteo (usa tus rutas)
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        return (new MailMessage)
            ->subject('Reestablecimiento de contraseña')
            ->greeting('Hola')
            ->line('Se ha recibido una solicitud para restablecer tu contraseña.')
            ->action('Restablecer ahora', $url)
            ->line('Si no realizaste esta solicitud, ignora este correo.');
    }
}
