Exportación Word de snapshot y comparador

Incluye:
- ExportRevisionContractualController
- ExportRevisionContractualWordService
- snippets de rutas
- snippets de botones en Blade

Dependencia requerida en Laravel:
composer require phpoffice/phpword

Pasos:
1. Instalar phpword.
2. Copiar archivos respetando rutas.
3. Agregar rutas en routes/web.php.
4. Agregar botones en las vistas.
5. Ejecutar:
   composer dump-autoload
   php artisan optimize:clear

Resultado:
- Exportación Word de snapshot individual
- Exportación Word del comparador de snapshots
