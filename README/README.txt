Rutas documentales actualizadas

Este ZIP incluye:
- routes/web.php ordenado y actualizado

Cambios incorporados:
- revisiones.documentos.show
- revisiones.documentos.preview
- revisiones.documentos.download

Orden aplicado:
1. index
2. store
3. show
4. preview
5. download
6. destroy

Pasos:
1. Reemplazar tu routes/web.php por el de este ZIP
2. Ejecutar:
   php artisan optimize:clear
   php artisan route:list | findstr documentos

Rutas esperadas:
- contractual.revisiones.documentos.index
- contractual.revisiones.documentos.store
- contractual.revisiones.documentos.show
- contractual.revisiones.documentos.preview
- contractual.revisiones.documentos.download
- contractual.revisiones.documentos.destroy
