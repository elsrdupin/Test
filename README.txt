/*****************************************************************************************
Autor: Ing. Diam Diaz
e-mai; el.sr.dupi@gmail.com
******************************************************************************************/

Esta es una prueb de concepto, usando Laravel Zero para desarrollar la prueba propuesta

Para instalar la aplicacion

- Ejecute composer update
- Cree el archivo .env (copie .env.testing)

Para ejecutar la aplicación: Ubicados en el directorio ./src ejecutamos el comando
    php euro lastdraw



Los archivos de interes (propios o relacionados al desarrollo, no del framework) son:


- Servicios, ./src/euromillions/**/*.*, contien los servicios que actualizan/persisten los datos los datos
- Configuracion composer.json, .env, ./src/config/[app.php, cache.php database.php euromillions.php]
- Pruebas ./test/**/*.*, .env.testing

Ante cualquier incoveniente puede comunicarse via e-mail, con gusto le atenderé.