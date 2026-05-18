Contradicciones con jerarquía real y documento prevalente

Incluye:
- DocumentContradictionDetectorService actualizado
- AnalisisRevisionContractualController ajustado para guardar documento prevalente
- Partial Blade para mostrar prevalencia, peso y motivo

Qué agrega:
1. Usa tipoDocumento.peso_jerarquico y jerarquía real
2. Determina documento prevalente preliminar
3. Maneja empate jerárquico como pendiente de verificar
4. Expone valor prevalente y motivo de prevalencia

Requisitos:
- documentos vinculados a tipo_documento_contractual_id
- catálogo con peso_jerarquico ya integrado
- snapshot y contradicciones persistidas funcionando

Pasos:
1. Copiar archivos
2. Ejecutar:
   composer dump-autoload
   php artisan optimize:clear
3. Ejecutar un nuevo análisis para que las nuevas contradicciones queden con prevalencia

Respaldo Git recomendado:
- después de validar una contradicción real con documento prevalente correcto
