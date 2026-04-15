<?php

namespace App\Http\Requests\Contractual;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoRevisionContractualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'archivo' => ['required', 'file', 'max:20480'],
            'tipo_documento' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.file' => 'El archivo cargado no es válido.',
            'archivo.max' => 'El archivo no puede superar los 20 MB.',
            'tipo_documento.max' => 'El tipo de documento no puede superar los 100 caracteres.',
        ];
    }
}