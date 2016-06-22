<?php
$contador = 0;
$archivo = file_get_contents('compiladores.txt');

if(!empty($_FILES['archivo']["tmp_name"])){
	$contador = 0;
	//$archivo = trim($_POST['codigo'])."\n";
	$archivo = file_get_contents($_FILES["archivo"]["tmp_name"]);
}

$palabras_reservadas = array ('leer', 'escribir', 'si', 'sino', 'finsi', 'para','finpara','mientras','finmientras',
                       'repetir','segun','con', 'paso', 'hacer','finsegun','hasta','algoritmo','inicio','entonces', 'hastaque', 'fin');

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

$otros = ["\n",
			" ",
		    "\0x000D000A", // [UNICODE] CR+LF: CR (U+000D) followed by LF (U+000A)
			"\0x000A",     // [UNICODE] LF: Line Feed, U+000A
	        "\0x000B",     // [UNICODE] VT: Vertical Tab, U+000B
	        "\0x000C",     // [UNICODE] FF: Form Feed, U+000C
	        "\0x000D",     // [UNICODE] CR: Carriage Return, U+000D
	        "\0x0085",     // [UNICODE] NEL: Next Line, U+0085
	        "\0x2028",     // [UNICODE] LS: Line Separator, U+2028
	        "\0x2029",     // [UNICODE] PS: Paragraph Separator, U+2029
	        "\0x0D0A",     // [ASCII] CR+LF: Windows, TOPS-10, RT-11, CP/M, MP/M, DOS, Atari TOS, OS/2, Symbian OS, Palm OS
	        "\0x0A0D",     // [ASCII] LF+CR: BBC Acorn, RISC OS spooled text output.
	        "\0x0A",       // [ASCII] LF: Multics, Unix, Unix-like, BeOS, Amiga, RISC OS
	        "\0x0D",       // [ASCII] CR: Commodore 8-bit, BBC Acorn, TRS-80, Apple II, Mac OS <=v9, OS-9
	        "\0x1E",       // [ASCII] RS: QNX (pre-POSIX)
	        //"\0x76",       // [?????] NEWLINE: ZX80, ZX81 [DEPRECATED]
	        "\0x15",
			'lf',        // Code: \n
			'cr',        // Code: \r
			'lfcr',      // Code: \n \r
			'crlf' ];

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
	global $contador, $archivo;

	if(php_sapi_name() == "cli"){
		print_r($resultado);

	}else{
		/*
		 * Construir tabla
		 */
		$colors             = ["PALABRA_RESERVADA"     => "navy",
							   "IDENTIFICADOR"         => "blue",
								"CADENA"               => "blue",
								"COMENTARIO"           => "green",
								"OP_DIVISION"          => "pumpkin",
								"ENTERO"               => "turquoise",
								"OP_SUMA"              => "pumpkin",
								"OP_INCREMENTO"        => "pumpkin",
								"OP_RESTA"             => "pumpkin",
								"OP_DECREMENTO"        => "pumpkin",
								"ASIGNACION"           => "pumpkin",
								"OP_MULTIPLICACION"    => "pumpkin",
								"COMA"                 => "orange",
								"PUNTO_COMA"           => "orange",
								"MAYOR_QUE"            => "orange",
								"MAYOR_IGUAL_QUE"      => "orange",
								"MENOR_QUE"            => "orange",
								"MENOR_IGUAL_QUE"      => "orange",
								"DIFERENTE_DE"         => "orange",
								"AND"                  => "navy",
								"OR"			       => "navy",
								"NOT"                  => "navy",
								"FLOTANTE"             => "turquoise",
								"PARENTESIS_IZQUIERDO" => "orange",
								"PARENTESIS_DERECHO"   => "orange",
								"ERROR"                => "red"];

		$tabla = '	<!DOCTPYE html>
					<html>
					<head>
						<meta charset="utf-8">
						<title>Analizador Léxico</title>
						<link rel="stylesheet" type="text/css" href="css/estilos.css">
					</head>
					<body>
						<h1>Analizador Léxico</h1>
						<table>
							<tr class="days">
								<th>Token</th>
								<th>Valor</th>
							</tr>';

		$i = 0;
		$num = count($resultado);
		foreach($resultado as $elemento){
			$color = $colors[$elemento['token']];

			if(++$i == $num){

			}elseif($elemento['token'] == "ERROR"){
				$tabla .= 	'<tr>'.
								'<td class="'.$color.'">'.$elemento['token'].'</td>'.
								'<td class="'.$color.'">Error en línea: '.$elemento['linea'].'</td>'.
							'</tr>';
			}else{
				$tabla .= 	'<tr>'.
								'<td class="'.$color.'">'.$elemento['token'].'</td>'.
								'<td class="'.$color.'">'.$elemento['valor'].'</td>'.
							'</tr>';
			}
		}

		$tabla .= 		'
					</table>

					<form method="POST" enctype="multipart/form-data">
						<h2>Código</h2>
						<br>
						<textarea name="codigo">'.$archivo.'</textarea>
						<br>

						<input type="file" name="archivo" id="archivo">
						<input type="submit" value="Actualizar">
					</form>

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

        if(array_key_exists($tipo["valor"], $matriz_trancisiones[$estado])){
            array_push($elemento, $char);
			$estadoA = $estado;
            $estado  = $matriz_trancisiones[$estado][$tipo["valor"]];
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
				if(!empty(trim($token['valor']))){
					array_push($resultado, $token);

				}elseif($token['valor'] == "0"){
					array_push($resultado, $token);
				}

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
			 * Si el tipo es un espacio entonces omitir mostrar el error
			 */
			if($tipo['tipo'] != "espacio"){
				$token['valor'] = implode("", $elemento);
				$token['token'] = "ERROR";
				$token['linea'] = $tipo["linea"];
				array_push($resultado, $token);
			}

            $estado   = 1;
            $elemento = array();
        }
    }

    imprimir_resultado_final($resultado);
}


function checar_tipo_caracter($char){
    global $simbolos, $otros, $contador;
	$result = array();
	$result['linea'] = $contador;

	if($char == "\n"){
		$contador++;
	}

    if(is_numeric($char)){
		$result["valor"] = "digito";
	    return $result;
    }

    if(in_array($char, $simbolos)){
		$result["valor"] = $char;
        return $result;
    }

    if(in_array($char, $otros)){
		$result["valor"] = "otro";
		$result["tipo"]  = "espacio";
        return $result;
    }

    if(is_string($char)){
		$result["valor"] = "letra";
        return $result;
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
