#!/usr/bin/php
<?php
$archivo = file_get_contents('compiladores.txt');

$palabras_reservadas = ['leer',
                       'escribir',
                       'si',
                       'sino',
                       'finsi',
                       'para',
                       'finpara',
                       'mientras',
                       'finmientras',
                       'repetir',
                       'segun',
                       'hacer',
                       'finsegun',
                       'hasta',
                       'algoritmo',
                       'inicio',
                       'entonces'];

//$posiciones_simbolos = ['letra' => 0,
//                        'digito' => 1,
//                        '.' => 2,
//                        ',' => 3,
//                        ';' => 4,
//                        '<' => 5,
//                        '>' => 6,
//                        '=' => 7,
//                        '"' => 8,
//                        '+' => 9,
//                        '-' => 10,
//                        '*' => 11,
//                        '/' => 12,
//                        '&' => 13,
//                        '|' => 14,
//                        '~' => 15,
//                        "otro" => 16];

$simbolos = ['.', ',', ';', '<', '>', '=', '"', '+', '*', '-', '/', '&', '|', '~'];

$estados_aceptacion = [3  => "IDENTIFICADOR",
                       7  => "CADENA",
                       12 => "COMENTARIO",
                       15 => "COMENTARIO",
                       16 => "OP_DIVISION",
                       18 => "ENTERO",
                       20 => "OP_SUMA",
                       21 => "OP_INCREMENTO",
                       23 => "OP_RESTA",
                       24 => "OP_DECREMENTO",
                       26 => "OP_MULTIPLICACION",
                       27 => "COMA",
                       28 => "PUNTO_COMA",
                       30 => "MAYOR_QUE",
                       31 => "MAYOR_IGUAL_QUE",
                       33 => "MENOR_QUE",
                       34 => "ASIGNACION",
                       35 => "MENOR_IGUAL_QUE",
                       36 => "DIFERENTE_DE",
                       37 => "AND",
                       38 => "OR",
                       39 => "NOT",
                       42 => "FLOTANTE"];

$matriz_trancisiones = [1 => ["letra" => 2, "digito" => 17, "," => 27, ";" => 28, "<" => 32, ">" => 29, "=" => 25, '"' => 4, "+" => 19, "-" => 22, "*" => 26, "/" => 8, "&" => 37, "|" => 38, "~" => 39],
                        2 => ["letra" => 2, "digito" => 17, "," => 27, ";" => 28, "<" => 32, ">" => 29, "=" => 25, '"' => 4, "+" => 19, "-" => 22, "*" => 26, "/" => 8, "&" => 37, "|" => 38, "~" => 39],
                        ];

//analizador_lexico($archivo);
//echo checar_tipo_caracter("\n")."\n";
//print_r($matriz_trancisiones[1]);

$a = in_array("letra", $matriz_trancisiones[1]);
echo $a."\n";

function analizador_lexico($archivo){
    /*
     * Longitud del archivo
     */
    global $matriz_trancisiones;
    $longitud_archivo = strlen($archivo);
    $estado = 1;
    $elemento = array();

    /*
     * Leer caracter por caracter
     */
    for($i = 0; $i <= $longitud_archivo; $i++){
        $char = substr( $archivo, $i, 1 );

        /*
         * Checar que tipo de caracter es
         */
        $tipo = checar_tipo_caracter($char);
echo $tipo;

//echo $estado."\n";

        if(in_array($tipo, $matriz_trancisiones[$estado])){
echo in_array($tipo, $matriz_trancisiones[$estado]);
            array_push($elemento, $char);
            $estado = $matriz_trancisiones[$estado][$tipo];
echo $estado."\n";
            /*
             * Checar estados final
             */

        }else{
            //$estado   = 0;
            $elemento = array();
            echo "Error";
        }
    }
}

function checar_tipo_caracter($char){
    global $simbolos;

    if(is_numeric($char)){
        return "digito";
    }

    if(in_array($char, $simbolos)){
        return $char;
    }

    if(is_string($char)){
        return "letra";
    }

    return "otro";
}

function es_palabra_reservada($palabra){
    global $palabras_reservadas;

    $palabra = strtolower($palabra);

    if(in_array($palabra, $palabrasReservadas)){
        return true;
    }

    return false;
}
?>
