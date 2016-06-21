#!/usr/bin/php
<?php
$archivo = file_get_contents('compiladores.txt');

$palabras_reservadas = array ('leer', 'escribir', 'si', 'sino', 'finsi', 'para','finpara','mientras','finmientras',
                       'repetir','segun','con', 'paso', 'hacer','finsegun','hasta','algoritmo','inicio','entonces', 'hastaque');

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

$simbolos = ['.', ',', ';', '<', '>', '=', '"', '+', '*', '-', '/', '&', '|', '~', '(', ')'];

$otros = ["\n", " "];

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
                       25 => "ASIGNACION",
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
                       42 => "FLOTANTE",
                       43 => "PARENTESIS_IZQUIERDO",
                       44 => "PARENTESIS_DERECHO"];

$matriz_trancisiones = [1  => ["letra" => 2, "digito"  => 17, "," => 27, ";" => 28, "<" => 32, ">" => 29, "=" => 25, '"' => 4, "+" => 19, "-" => 22, "*" => 26, "/" => 8, "&" => 37, "|" => 38, "~" => 39, "(" => 43, ")" => 44],
                        2  => ["letra" => 2, "digito"  => 2 , "." => 3 , "," => 3 , ";" => 3 , "<" => 3 , ">" => 3 , "=" => 3, '"' => 3 , "+" => 3 , "-" => 3 , "*" => 3, "/" => 3 , "&" => 3 , "|" => 3 , "~" => 3, "(" => 3, ")" => 3, "otro" => 3],
                        4  => ["letra" => 5, "digito"  => 5 , "." => 5 , "," => 5 , ";" => 5 , "<" => 5 , ">" => 5 , "=" => 5, '"' => 6 , "+" => 5 , "-" => 5 , "*" => 5, "/" => 5 , "&" => 5 , "|" => 5 , "~" => 5, "(" => 5, ")" => 5, "otro" => 5],
                        5  => ["letra" => 5, "digito"  => 5 , "." => 5 , "," => 5 , ";" => 5 , "<" => 5 , ">" => 5 , "=" => 5, '"' => 6 , "+" => 5 , "-" => 5 , "*" => 5, "/" => 5 , "&" => 5 , "|" => 5 , "~" => 5, "(" => 5, ")" => 5, "otro" => 5],
                        6  => ["letra" => 7, "digito"  => 7 , "." => 7 , "," => 7 , ";" => 7 , "<" => 7 , ">" => 7 , "=" => 7, '"' => 7 , "+" => 7 , "-" => 7 , "*" => 7, "/" => 7 , "&" => 7 , "|" => 7 , "~" => 7, "(" => 7, ")" => 7, "otro" => 7],
                        8  => ["letra" => 16, "digito" => 16, "." => 16, "," => 16, ";" => 16, "<" => 16, ">" => 16, "=" => 16, '"' => 16, "+" => 16, "-" => 16 , "*" => 9, "/" => 13 , "&" => 16, "|" => 16, "~" => 16, "(" => 16, ")" => 16, "otro" => 16],
                        9  => ["letra" => 10, "digito" => 10, "otro" => 10],
                        10 => ["letra" => 10, "digito" => 10, "otro" => 10, "*" => 11],
                        11 => ["/" => 12],
                        13 => ["letra" => 14, "digito" => 14],
                        14 => ["letra" => 14, "digito" => 14, "." => 15, "," => 15, ";" => 15, "<" => 15, ">" => 15, "=" => 15, '"' => 15, "+" => 15, "-" => 15, "*" => 15, "/" => 15, "&" => 15, "|" => 15, "~" => 15, "(" => 15, ")" => 15, "otro" => 15],
                        17 => ["letra" => 18, "digito" => 17, "." => 40, "," => 18, ";" => 18, "<" => 18, ">" => 18, "=" => 18, '"' => 18, "+" => 18, "-" => 18, "*" => 18, "/" => 18, "&" => 18, "|" => 18, "~" => 18, "(" => 18, ")" => 18, "otro" => 18],
                        19 => ["letra" => 20, "digito" => 20, "." => 20, "," => 20, ";" => 20, "<" => 20, ">" => 20, "=" => 20, '"' => 20, "+" => 21, "-" => 20, "*" => 20, "/" => 20, "&" => 20, "|" => 20, "~" => 20, "(" => 20, ")" => 20, "otro" => 20],
                        22 => ["letra" => 23, "digito" => 23, "." => 23, "," => 23, ";" => 23, "<" => 23, ">" => 23, "=" => 23, '"' => 23, "+" => 23, "-" => 24, "*" => 23, "/" => 23, "&" => 23, "|" => 23, "~" => 23, "(" => 23, ")" => 23, "otro" => 23],
                        29 => ["letra" => 30, "digito" => 30, "." => 30, "," => 30, ";" => 30, "<" => 30, ">" => 30, "=" => 31, '"' => 30, "+" => 30, "-" => 30, "*" => 30, "/" => 30, "&" => 30, "|" => 30, "~" => 30, "(" => 30, ")" => 30, "otro" => 30],
                        32 => ["letra" => 33, "digito" => 33, "." => 33, "," => 33, ";" => 33, "<" => 33, ">" => 36, "=" => 35, '"' => 33, "+" => 33, "-" => 34, "*" => 33, "/" => 33, "&" => 33, "|" => 33, "~" => 33, "(" => 33, ")" => 33, "otro" => 33],
                        40 => ["digito" => 41],
                        41 => ["letra" => 42, "digito" => 41, "." => 42, "," => 42, ";" => 42, "<" => 42, ">" => 42, "=" => 42, '"' => 42, "+" => 42, "-" => 42, "*" => 42, "/" => 42, "&" => 42, "|" => 42, "~" => 42, "(" => 42, ")" => 42, "otro" => 42],
                        ];

/*
 * LLamamos a la funcion principal e iniciamos
 */
analizador_lexico($archivo);

function imprimir_resultado_final($resultado){
	if(php_sapi_name() == "cli"){
		print_r($resultado);

	}else{
		/*
		 * Construir tabla
		 */
		$colors = array('blue', 'purple', 'navy', 'green', 'orange', 'pumpkin', 'turquoise');

		$tabla = '	<!DOCTPYE html>
					<html>
					<head>
						<meta charset="utf-8">
						<title>Analizador Léxico</title>
						<link rel="stylesheet" type="text/css" href="css/estilos.css">
					</head>
					<body>
						<table>
							<tr class="days">
								<th>Token</th>
								<th>Valor</th>
							</tr>';

		foreach($resultado as $elemento){
			$color = $colors[array_rand($colors, 1)];

			$tabla .= 	'<tr>'.
							'<td class="'.$color.'">'.$elemento['token'].'</td>'.
							'<td class="'.$color.'">'.$elemento['valor'].'</td>'.
						'</tr>';
		}

		$tabla .= '		</table>
					</body>
					</html>';

		echo $tabla;
	}
}

function imprimir($valor){
	if(is_array($valor)){
		echo '<pre>';
		print_r($valor);
		echo '</pre>';

	}else{
		echo $valor."\n";
	}
}

function analizador_lexico($archivo){
    /*
     * Longitud del archivo
     */
    global $matriz_trancisiones, $estados_aceptacion, $palabras_reservadas;
    $longitud_archivo = strlen($archivo);
    $estado           = 1;
    $elemento         = array();
    $resultado        = array();
    $band             = false;

    /*
     * Leer caracter por caracter
     */
    for($i = 0; $i <= $longitud_archivo; $i++){
        /*
         * Obtener caracter
         */
        if($band){
            $i--;
            $char = substr( $archivo, $i, 1 );
            $band = false;
        }

        $char = substr( $archivo, $i, 1 );

        /*
         * Checar que tipo de caracter es
         */
        $tipo = checar_tipo_caracter($char);

        if(array_key_exists($tipo, $matriz_trancisiones[$estado])){
            array_push($elemento, $char);
			$estadoA = $estado;
            $estado  = $matriz_trancisiones[$estado][$tipo];
            /*
             * Checar si es un estado de aceptacion
             */
            if(array_key_exists($estado, $estados_aceptacion)){
                /*
                 * Obtener estado de aceptacion
                 */
				$token['estadoAnterior'] = $estadoA;
				$token['estadoPosterior'] = $estado;
                $token['token'] = $estados_aceptacion[$estado];
                $token['valor'] = implode("", $elemento);

                /*
                 * Si es un identificador debemos corroborar
                 * si es o no una palabra reservada
                 */
                if($token['token'] == "IDENTIFICADOR"){
                    if(in_array(trim(strtolower($token['valor'])), $palabras_reservadas)){
                        $token['token'] = "PALABRA_RESERVADA";
                    }
                }

                if($token['token'] == "IDENTIFICADOR" or $token['token'] == "CADENA" or $token['token'] == "ENTERO" or $token['token'] == "OP_SUMA" or $token['token'] == "OP_RESTA"){
                    $token['valor'] = substr($token['valor'], 0, -1);
                    $band = true;
                }

                /*
                 * Imprimir el elemento (token, valor)
                 */
                //imprimir("----------------------------------------");
                //imprimir("ACEPTADO");
                //imprimir($token);
                //imprimir("----------------------------------------");
                array_push($resultado, $token);

                /*
                 * Vaciar elemento para volver a construir otro
                 * y resetear estado
                 */
                $token    = array();
                $elemento = array();
                $estado   = 1;
            }

        }else{
            /*
             * Imprimir el elemento (token, valor)
             */
            //imprimir("----------------------------------------");
            //imprimir("ERROR");
            //imprimir($elemento);
            //imprimir("----------------------------------------");
            $token['valor'] = implode("", $elemento);
            $token['token'] = "ERROR";
            array_push($resultado, $token);

            $estado   = 1;
            $elemento = array();
        }
    }

    imprimir_resultado_final($resultado);
}

function checar_tipo_caracter($char){
    global $simbolos, $otros;

    if(is_numeric($char)){
        return "digito";
    }

    if(in_array($char, $simbolos)){
        return $char;
    }

    if(in_array($char, $otros)){
        return "otro";
    }

    if(is_string($char)){
        return "letra";
    }
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
