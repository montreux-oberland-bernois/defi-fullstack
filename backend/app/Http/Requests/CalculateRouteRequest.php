<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CalculateRouteRequest extends FormRequest
{
    /**
     * Codes analytiques valides pour catégoriser les trajets.
     */
    public const VALID_ANALYTIC_CODES = ['FRET', 'PASS', 'MAINT', 'TEST'];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fromStationId' => 'required|string|max:10',
            'toStationId' => 'required|string|max:10',
            'analyticCode' => 'required|string|in:'.implode(',', self::VALID_ANALYTIC_CODES),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fromStationId.required' => 'La station de départ est obligatoire',
            'fromStationId.string' => 'La station de départ doit être une chaîne de caractères',
            'fromStationId.max' => 'La station de départ ne doit pas dépasser :max caractères',
            'toStationId.required' => "La station d'arrivée est obligatoire",
            'toStationId.string' => "La station d'arrivée doit être une chaîne de caractères",
            'toStationId.max' => "La station d'arrivée ne doit pas dépasser :max caractères",
            'analyticCode.required' => 'Le code analytique est obligatoire',
            'analyticCode.string' => 'Le code analytique doit être une chaîne de caractères',
            'analyticCode.in' => 'Le code analytique doit être l\'un des suivants : '.implode(', ', self::VALID_ANALYTIC_CODES),
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'code' => 'VALIDATION_ERROR',
                'message' => 'Requête invalide',
                'details' => $validator->errors()->all(),
            ], 400)
        );
    }
}
