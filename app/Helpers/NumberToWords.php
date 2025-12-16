<?php

namespace App\Helpers;

class NumberToWords
{
    private static $unidades = [
        '', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove',
        'dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezessete', 'dezoito', 'dezenove'
    ];

    private static $dezenas = [
        '', '', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa'
    ];

    private static $centenas = [
        '', 'cento', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos'
    ];

    public static function toWords($number)
    {
        if ($number == 0) {
            return 'zero';
        }

        $number = (float) $number;
        $reais = (int) $number;
        $centavos = (int) round(($number - $reais) * 100);

        $result = self::convert($reais) . ' reais';
        
        if ($centavos > 0) {
            $result .= ' e ' . self::convert($centavos) . ' centavos';
        }

        return $result;
    }

    private static function convert($number)
    {
        if ($number == 0) {
            return '';
        }

        if ($number < 20) {
            return self::$unidades[$number];
        }

        if ($number < 100) {
            $dezena = (int) ($number / 10);
            $unidade = $number % 10;
            
            $result = self::$dezenas[$dezena];
            if ($unidade > 0) {
                $result .= ' e ' . self::$unidades[$unidade];
            }
            
            return $result;
        }

        if ($number < 1000) {
            $centena = (int) ($number / 100);
            $resto = $number % 100;
            
            if ($centena == 1 && $resto == 0) {
                return 'cem';
            }
            
            $result = self::$centenas[$centena];
            if ($resto > 0) {
                $result .= ' e ' . self::convert($resto);
            }
            
            return $result;
        }

        if ($number < 1000000) {
            $milhar = (int) ($number / 1000);
            $resto = $number % 1000;
            
            $result = self::convert($milhar) . ' mil';
            if ($resto > 0) {
                $result .= ' e ' . self::convert($resto);
            }
            
            return $result;
        }

        if ($number < 1000000000) {
            $milhao = (int) ($number / 1000000);
            $resto = $number % 1000000;
            
            $result = self::convert($milhao);
            if ($milhao == 1) {
                $result .= ' milhão';
            } else {
                $result .= ' milhões';
            }
            
            if ($resto > 0) {
                $result .= ' e ' . self::convert($resto);
            }
            
            return $result;
        }

        return 'número muito grande';
    }
}
