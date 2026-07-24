<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiplomadoLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'pais' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'programa_id' => 'required|exists:programas,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required' => 'Por favor ingresa tus nombres.',
            'apellidos.required' => 'Por favor ingresa tus apellidos.',
            'correo.required' => 'Por favor ingresa tu correo electrónico.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
            'pais.required' => 'Por favor indica tu país.',
            'region.required' => 'Por favor indica tu región o departamento.',
            'telefono.required' => 'Por favor ingresa tu teléfono.',
            'programa_id.required' => 'Por favor selecciona el diplomado de tu interés.',
            'programa_id.exists' => 'El diplomado seleccionado no es válido.',
        ];
    }
}
