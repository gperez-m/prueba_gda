# prueba_gda
Prueba tecnica de GDA

# Instalación

Se requiere :
PHP>=8.2
Composer >= 2.1

En consola o terminal correr los siguientes comandos en la raiz del proyecto

composer install

Una vez instalado hay que configurar el .env para la conexion a la BD y generar el key del JWT

php key:generator
php artisan migrate
php artisan db:seed

copiar el .env.example a .env

para levantar el servidor de manera local correr el comando

php artisan serve





