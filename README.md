# PHP-BFD : PHP Brute Force Defense
Este script permite leer los archivos de registro (logs) y consulta los eventos comunes de intentos de ataque de fuerza bruta, como ejemplo: "wrong password" o similar. El script extrae las ips del registro (log) que coincidan con los terminos de busqueda configurados en el script y envia un comando a la consola para activar el bloqueo de la IP "agresora" en el firewall.

Inicie a programar este script por una necesidad puntual: Proteger las extensiones de un servidor Asterisk de intentos de ataque de fuerza bruta y aproveche la oportunidad para desarrollar algo propio que pueda aplicarse a diversos casos.

El script permite personalizar los siguientes campos para su funcionamiento:

$ruta_log_asterisk  : Permite definir el log con el cual desea interactuar.

$ssh                : Comando a enviar a la consola.

$whitelist          : Lista de IP's de confianza que deseamos el script ignore.

$casos              : Define los eventos a buscar en el registro, como "wrong password", "unknown user", etc.

$ocurrencias        : Define cuantas veces debe de reincidir un IP para ser bloqueada

El script es bastante simple y se adapta a cualquier necesidad.

¡Salud!
