<?php

/**
 * @author Luigi Salas www.luigisalas.com
 * @copyright 2016
 */

/**
 * CONFIGURAMOS EL SCRIPT
*/
// Ruta al archivo LOG de asterisk
$ruta_log_asterisk = "/var/logs/asterisk/full";

// Cuantas ocurrencias deben ocurrir en una ip para generar un error
$ocurrencias = 5;

// Comando SSH del firewall (El valor %ip% sera reemplazado por la IP a bloquear)
$ssh = "iptables -I INPUT -s %ip% -j DROP";

// Direcciones IP ignoradas por el script
$whitelist[] = "192.168.1.1";
$whitelist[] = "192.168.1.2";

// TRUE = no envia los comandos SHH pero si registra el log.txt. FALSE = Envia los comandos SSH y registra log.txt
$debug = FALSE;

/**
 * CODIGO DEL SCRIPT
*/

// Casos de Asterisk a revisar
$casos = array();

// ASTERISK: SIP wrong password
$casos[] = "wrong password";
# ASTERISK: SIP no extension
$casos[] = "No matching peer found";
# ASTERISK: IAX2 auth failed
$casos[] = "failed MD5 authentication";
# ASTERISK: Transmission error of Packet - operation not permitted
$casos[] = "RTP Transmission error of packet";
# ASTERISK: Registration from - Username/auth name mismatch
$casos[] = "Username/auth name mismatch";
# ASTERISK: Registration from - Device does not match ACL
$casos[] = "Device does not match ACL";
# ASTERISK: Host - failed to authenticate as
$casos[] = "failed to authenticate as";
# ASTERISK: No registration for peer
$casos[] = "No registration for peer";
# ASTERISK: Sip Failed to authenticate user
$casos[] = "Failed to authenticate user";

// Definimos el encabezado
header("Content-Type: text/plain");

// Declaramos variables
$ips        = array();  // Definimos un array donde almacenaremos las ips encontradas y sus ocurrencias

// Abrimos el log de asterisk
$log_asterisk = @fopen($ruta_log_asterisk, "r") or exit("Imposible abrir el archivo $ruta_log_asterisk :(");

// Abrimos el log del script
$log = fopen("log.txt", "a+");

// Comparamos las lineas del log 
while(!feof($log_asterisk)){
    
    $linea = fgets($log_asterisk);
    // Recorremos el array de casos a comparar
    foreach($casos as $caso){
        // Convertimos cadenas a minuscula para evitar incompatibilidades
        $linea = strtolower($linea);
        $caso = strtolower($caso);
        // Comparamos cada linea con los casos definidos
        $comparacion = strpos($linea, $caso);
        // Verificamos los casos encontramos
        if($comparacion){
            // Buscamos IPs
            preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $linea, $ip_matches);
            # las ips encontradas se almacenan en el array $ip_matches[0]
        }
    } // TERMINA foreach

    // Si no se han encontrado ips en la linea del log, continuamos el script
    if(!isset($ip_matches)) continue;

    // Verificamos las ips encontradas ($ip_matches) no esten en el array $whitelist
    foreach($ip_matches[0] AS $ip_match){
        // realizamos la busqueda de aquellas ips NO listadas en nuestro whitelist
        if (!in_array($ip_match, $whitelist)) {
            // Al existir agregamos la ip al array $ips
            // Si la ip ya ha cuenta en el array $ips sumamos su ocurrencia
            if(isset($ips[$ip_match])){
                $ips[$ip_match] = $ips[$ip_match]+1;
            // Si ips no existe en el array $ips lo declaramos
            } else {
                $ips[$ip_match] = 1;    
            }   
        }
    } // TERMINA foreach
    
    // Destruimos variables para evitar acumulacion de datos
    unset($ip_matches);
    
} // TERMINA fopen - analisis del log de asteris

// Recorremos el array $ips para enviar los bloqueos al firewall por SSH;
foreach($ips as $ip => $count){
    // verificamos se cumplan las ocurrencias minimas
    if($count >= $ocurrencias){
        // preparamos el comando a enviar
        $cmd = str_replace ( "%ip%" , $ip , $ssh); // reemplazamos el valor de %ip%
        // Enviamos el comando por consulta al firewall
        if(!$debug) shell_exec($cmd);
        // registramos el evento en un .txt
        $log_linea = date('Y-m-d H:m:s')." - Se bloquea IP $ip por tener $count ocurrencias.\r\n";
        fputs($log, $log_linea);
        
    }
}

// Cerramos el log de asterisk
fclose($log_asterisk);
// Cerramos el log del script
fclose($log);

?>