<?php

namespace App\Http\Requests\Api\Auth;

use App\Application\UseCase\LoginUser\LoginUserCommand;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function toCommand(): LoginUserCommand
    {
        return new LoginUserCommand(
            email: $this->input('email'),
            password: $this->input('password')
        );
    }
}
