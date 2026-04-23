Evaluación de cierre de revisión

Incluye:
- RevisionClosureEvaluationService
- partial Blade para mostrar estado de cierre
- snippets de integración para snapshot controller, snapshot blade y reporte

Qué hace:
- clasifica la revisión como apta, observada o no apta
- usa gate documental, hallazgos, contradicciones y evolución
- entrega motivos claros y un resumen ejecutivo de cierre

Pasos:
1. Copiar archivos
2. Integrar snippet en SnapshotRevisionContractualController@show
3. Incluir partial en snapshot/show.blade.php
4. Opcional: reflejarlo en el reporte profesional
5. Ejecutar:
   composer dump-autoload
   php artisan optimize:clear

Resultado:
- apoyo a decisión final asistida
- criterio visible de cierre
- mejor trazabilidad para auditoría y jefatura
