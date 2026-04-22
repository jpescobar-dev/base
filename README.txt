Topnavbar con breadcrumbs dinámicos

Incluye:
- topnavbar.blade.php actualizado
- TopnavbarBreadcrumbsComposer.php
- ViewComposerServiceProvider.php

Qué hace:
- agrega breadcrumbs según la ruta actual
- mantiene alineación con el contenido
- funciona para dashboard, usuarios, revisiones, documentos y snapshots

Instalación:
1. Copiar archivos respetando rutas
2. Registrar el provider en config/app.php:
   App\Providers\ViewComposerServiceProvider::class,
3. En app.blade.php mantener el topnavbar dentro de:
   <div id="content" class="main-content">
       <div class="layout-px-spacing">
           @include('layouts.theme.partials.topnavbar')
           @yield('content')
       </div>
   </div>
4. Ejecutar:
   php artisan optimize:clear

Respaldo repo:
Después de aplicar esto y verificar visualmente, sí conviene hacer commit y push.
