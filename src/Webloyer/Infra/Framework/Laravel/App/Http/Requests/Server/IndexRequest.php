<?php

declare(strict_types=1);

namespace Webloyer\Infra\Framework\Laravel\App\Http\Requests\Server;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRedirectUrl(): string
    {
        return route('servers.index');
    }
}
