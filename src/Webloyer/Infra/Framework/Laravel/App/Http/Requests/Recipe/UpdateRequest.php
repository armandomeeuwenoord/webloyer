<?php

declare(strict_types=1);

namespace Webloyer\Infra\Framework\Laravel\App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|list<string>>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'body'        => ['required', 'string'],
        ];
    }
}
