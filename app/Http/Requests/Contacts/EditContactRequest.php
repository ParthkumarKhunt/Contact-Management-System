<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditContactRequest extends FormRequest
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
        $contactId = $this->route('contact');

        return [
            'name' => 'required|string|min:2|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('contacts', 'email')->ignore($contactId)
            ],
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
            'custom_fields' => 'nullable|array',
            'custom_fields.*' => 'nullable',
            'custom_fields_value' => 'nullable|array',
            'custom_fields_value.*' => 'nullable',
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
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least :min characters.',
            'name.max' => 'The name may not be greater than :max characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email has already been taken.',

            'phone.string' => 'The phone must be a string.',
            'phone.max' => 'The phone may not be greater than :max characters.',

            'gender.in' => 'The selected gender is invalid.',

            'profile_image.image' => 'The profile image must be an image.',
            'profile_image.mimes' => 'The profile image must be a file of type: :values.',
            'profile_image.max' => 'The profile image may not be greater than :max kilobytes.',

            'additional_file.file' => 'The additional file must be a file.',
            'additional_file.mimes' => 'The additional file must be a file of type: :values.',
            'additional_file.max' => 'The additional file may not be greater than :max kilobytes.',

            'custom_fields.array' => 'The custom fields must be an array.',
            'custom_fields_value.array' => 'The custom field values must be an array.',
        ];
    }
}
