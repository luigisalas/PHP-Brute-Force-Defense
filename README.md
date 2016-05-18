# PHP-BFD : PHP Brute Force Defense
Este script permite leer los archivos de registro (logs) y consulta los eventos comunes de intentos de ataque de fuerza bruta, como ejemplo: "wrong password" o similar. El script extrae las ips del registro (log) que coincidan con los terminos de busqueda configurados en el script y envia un comando a la consola para activar el bloqueo de la IP "agresora" en el firewall.

Inicie a programar este script por una necesidad puntual: Proteger las extensiones de un servidor Asterisk de intentos de ataque de fuerza bruta y aproveche la oportunidad para desarrollar algo propio que pueda aplicarse a diversos casos.

El script puede leer cualquier archivo de registro (log) y enviar comandos personalizados a la consola, interactuar con varios firewalls.
