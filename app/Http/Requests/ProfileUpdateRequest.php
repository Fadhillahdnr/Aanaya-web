<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\AllowedEmailDomain;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^\+?[0-9][0-9\s().-]{7,28}$/',
            ],
            'address' => ['nullable', 'string', 'max:1000'],
            'gender' => [
                'nullable',
                Rule::in(['male', 'female', 'non_binary', 'prefer_not_to_say']),
            ],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'uploaded_media.profile_photo' => [
                'nullable',
                'string',
                'exists:media,id',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::when(
                    strtolower((string) $this->input('email')) !== strtolower((string) $this->user()->email),
                    [new AllowedEmailDomain]
                ),
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number, for example 08123456789.',
            'date_of_birth.before_or_equal' => 'The date of birth cannot be in the future.',
        ];
    }
}
