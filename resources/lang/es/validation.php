<?php

return [

    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El campo :attribute debe ser un email válido.',
    'confirmed' => 'Las contraseñas no coinciden.',

    'unique' => 'Ya existe una cuenta con este :attribute.',

    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],

    'image' => 'El campo :attribute debe ser una imagen.',

    'max' => [
        'file' => 'La imagen es demasiado grande.',
    ],

    'password' => [
        'letters' => 'La contraseña debe contener al menos una letra.',
        'mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
        'numbers' => 'La contraseña debe contener al menos un número.',
        'symbols' => 'La contraseña debe contener al menos un símbolo.',
        'uncompromised' => 'La contraseña ha aparecido en una filtración de datos. Elige otra.',
    ],

    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'image' => 'imagen',
    ],

    'uploaded' => 'Error al subir la imagen. Inténtalo de nuevo o prueba otra foto.',
];