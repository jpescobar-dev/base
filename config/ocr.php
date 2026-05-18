<?php

return [
    'enabled' => env('OCR_ENABLED', true),
    'tesseract_binary' => env('OCR_TESSERACT_BINARY'),
    'pdftoppm_binary' => env('OCR_PDFTOPPM_BINARY'),
    'language' => env('OCR_LANGUAGE', 'spa'),
    'dpi' => (int) env('OCR_DPI', 300),
    'max_pages' => (int) env('OCR_MAX_PAGES', 20),
    'temp_dir' => storage_path('app/ocr_temp'),
    'pdftotext_binary' => env('OCR_PDFTOTEXT_BINARY'),

    
];
