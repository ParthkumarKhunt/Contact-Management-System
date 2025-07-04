<?php

namespace App\Http\Requests\CustomFields;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditCustomFieldsRequest extends FormRequest
{
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
        $customFieldId = $this->route('custom_field');

        return [
            'label' => [
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('custom_fields', 'label')->ignore($customFieldId)
            ],
            'type' => 'required|string|in:text,email,phone,date,number,textarea,select',
            'options' => 'nullable|required_if:type,select|string',
            'active' => 'nullable|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'label.required' => 'The field label is required.',
            'label.string' => 'The field label must be a string.',
            'label.min' => 'The field label must be at least :min characters.',
            'label.max' => 'The field label may not be greater than :max characters.',
            'label.unique' => 'This label has already been taken.',

            'type.required' => 'Please select a field type.',
            'type.in' => 'The selected field type is invalid.',

            'options.required_if' => 'Options are required when the field type is "select".',
            'options.string' => 'The options must be a string.',
        ];
    }
}
