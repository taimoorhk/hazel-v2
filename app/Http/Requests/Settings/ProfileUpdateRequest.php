<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get user from database by email if not available in session
        $user = $this->user();
        if (!$user) {
            $email = $this->input('email');
            if ($email) {
                $user = User::where('email', $email)->first();
            }
        }
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user?->id),
            ],
            'user_address' => ['nullable', 'string', 'max:500'],
            'user_phone_number' => ['nullable', 'string', 'max:20'],
            'user_pronouns' => ['nullable', 'string', 'max:50'],
        ];
    }
}
