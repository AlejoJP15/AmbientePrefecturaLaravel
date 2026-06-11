<?php
namespace App\Helpers;

class ValidacionRuc
{
    public static function validar($ruc)
    {
        if (!is_string($ruc) || strlen($ruc) !== 13 || !ctype_digit($ruc)) {
            return false;
        }

        // Debe terminar en 001
        if (substr($ruc, -3) !== '001') {
            return false;
        }

        // Evitar secuencias como 0000000000000, 1111111111111, etc.
        if (preg_match('/^(\d)\1{12}$/', $ruc)) {
            return false;
        }

        $provincia = intval(substr($ruc, 0, 2));
        if ($provincia < 1 || $provincia > 24) {
            return false;
        }

        $tercerDigito = intval($ruc[2]);
        $coeficientes = [];
        $longitud = 0;
        $modulo = 0;
        $digitoVerificador = 0;

        if ($tercerDigito >= 0 && $tercerDigito <= 5) {
            // Persona natural
            $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
            $longitud = 10;
            $modulo = 10;
        } elseif ($tercerDigito == 6) {
            // Sociedad pública
            $coeficientes = [3, 2, 7, 6, 5, 4, 3, 2];
            $longitud = 9;
            $modulo = 11;
        } elseif ($tercerDigito == 9) {
            // Sociedad privada
            $coeficientes = [4, 3, 2, 7, 6, 5, 4, 3, 2];
            $longitud = 10;
            $modulo = 11;
        } else {
            return false; // Tercer dígito inválido
        }

        $suma = 0;
        for ($i = 0; $i < count($coeficientes); $i++) {
            $valor = intval($ruc[$i]) * $coeficientes[$i];

            if ($modulo == 10 && $valor >= 10) {
                $valor -= 9;
            }

            $suma += $valor;
        }

        $resultado = $suma % $modulo;
        if ($resultado == 0) {
            $digitoVerificador = 0;
        } else {
            $digitoVerificador = $modulo - $resultado;
        }

        return intval($ruc[$longitud - 1]) === $digitoVerificador;
    }
}
