<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de lenguaje de validación
    |--------------------------------------------------------------------------
    */

    'accepted' => ':attribute debe ser aceptado.',
    'email'    => 'El :attribute debe ser una dirección de correo válida.',
    'required' => ':attribute es obligatorio.',
    'unique'   => 'El :attribute ya está en uso.',
    'min'      => [
        'string' => ':attribute debe tener al menos :min caracteres.',
    ],

    // Reglas modernas de Password (Illuminate\Validation\Rules\Password)
    'password' => [
        'letters'       => 'La contraseña debe contener al menos una letra.',
        'mixed'         => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.',
        'numbers'       => 'La contraseña debe contener al menos un número.',
        'symbols'       => 'La contraseña debe contener al menos un símbolo.',
        'uncompromised' => 'La contraseña proporcionada aparece en una filtración de datos. Elige otra diferente.',
    ],

    // Mensajes personalizados por campo/regla
    'custom' => [
        'usuario' => [
            'unique' => 'El usuario ya está registrado.',
        ],
        'email' => [
            'unique' => 'El correo electrónico ya está registrado.',
        ],
        // agrega más campos si lo necesitas...
    ],

    // Alias legibles de los atributos
    'attributes' => [
        'usuario'     => 'usuario',
        'email'       => 'correo electrónico',
        'contraseña'  => 'contraseña',
        'current_password'     => 'contraseña actual',
        'password'             => 'contraseña',
        'password_confirmation'=> 'confirmación de contraseña',
    ],

];
