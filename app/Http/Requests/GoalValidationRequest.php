<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class GoalValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //TODO give a more strict path location validation
        return [
            "name" => "required|string|max:50",
            "details" => "required|string|max:150",
            "target_amount" => "numeric|required|integer",
            "start_date" => "sometimes|date",
            "end_date" => "date|required",
            "path_location" => "ends_with:jpeg,jpg,png",
            "has_image_proof" => "required",
            "has_video_proof" => "required",
            "type_id" => "required|numeric",
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json($errors,  400)
        );
    }
}
