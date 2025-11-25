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
            'fromStationId.required' => __('messages.from_station_required'),
            'fromStationId.string' => __('messages.from_station_string'),
            'fromStationId.max' => __('messages.from_station_max'),
            'toStationId.required' => __('messages.to_station_required'),
            'toStationId.string' => __('messages.to_station_string'),
            'toStationId.max' => __('messages.to_station_max'),
            'analyticCode.required' => __('messages.analytic_code_required'),
            'analyticCode.string' => __('messages.analytic_code_string'),
            'analyticCode.in' => __('messages.analytic_code_invalid', ['codes' => implode(', ', self::VALID_ANALYTIC_CODES)]),
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
                'message' => __('messages.invalid_request'),
                'details' => $validator->errors()->all(),
            ], 400)
        );
    }
}
