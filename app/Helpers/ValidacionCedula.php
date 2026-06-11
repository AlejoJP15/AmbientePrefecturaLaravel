<?php

namespace App\Helpers;

class ValidacionCedula
{
    public static function validar($cedula)
    {
        if (strlen($cedula) != 10 || !is_numeric($cedula)) {
            return false;
        }

        $digitos = str_split($cedula);
        $provincia = (int)substr($cedula, 0, 2);
        $tercerDigito = (int)$cedula[2];

        if ($provincia < 1 || $provincia > 24 || $tercerDigito > 6) {
            return false;
        }

        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $valor = (int)$digitos[$i];

            if ($i % 2 == 0) {
                $valor *= 2;
                if ($valor > 9) {
                    $valor -= 9;
                }
            }

            $suma += $valor;
        }

        $verificador = (10 - ($suma % 10)) % 10;

        return $verificador == (int)$digitos[9];
    }
}