##  WORKFLOW ENTRE SERVIDOR / CLINTE


CLIENTE--------------------------------------------------------SERVIDOR
APIID + APIPASSWD(POST)------------------------------->Verifica las credenciales
SESSION<----------------------------------------------(POST)Asigna un token para la sessión
AMBITO+FUNCION(GET)PARAMETROS+SESSION(POST)----------->Procesa la peticion
Recibe respuesta<--------------------------------------Encripta los datos y les envía(POST)

Creación de la sessión
----------------------

1.El cliente envía por POST su ID y el Hash del Password.
2.El servidor valída los datos y retorna al cliente el TOKEN de la session
3.El cliente debe guardar este token para poder realizar las peticiones

Llamadas a las funciones
------------------------

1. El cliente envía por GET el AMBITO/FUNCION que deséa ejecutar, mientras que por POST debe remitir el token de la SESSION, PARAMETROS para la función(CIFRADOS) y el NONCE de ejecución
2. El Servidor valida que la Sessión y el NONCE sean correctos. Intentará descifrar los datos con los certificados del usuario y ejecutará la función.
3. El servidor enviará de nuevo al cliente los datos resultantes de la función(CIFRADOS).
4. El cliente Descifrará los datos y procederá a su utilización.

Llamadas adicionales
--------------------

. El cliente puede realizar varias llamadas con la misma session, siempre desde el mismo equipo(IP,CABECERAS, ETC...) sin tener que renovar la SESSION.
. Si la sessión está caducada se remitira una alerta para generar una nueva.
