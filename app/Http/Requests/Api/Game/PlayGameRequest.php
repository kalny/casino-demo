<?php

namespace App\Http\Requests\Api\Game;

use App\DTO\Api\Game\PlayGameDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PlayGameRequest extends FormRequest
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
            'amount' => ['required', 'integer', 'min:1'],
            'params' => ['nullable', 'array'],
        ];
    }

    public function getDTO(): PlayGameDTO
    {
        return new PlayGameDTO(
            amount: $this->validated('amount'),
            params: $this->validated('params'),
        );
    }
}
