<?php

namespace TechStudio\Core\app\Http\Requests\TroubleshootingReport;

use App\Http\Requests\User\BaseUserValidationRequest;
use Illuminate\Foundation\Http\FormRequest;
use TechStudio\Core\app\Models\Report;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report' => ['required', 'string'],
            'reportableId' => ['required', 'string'],
            'reportableType' => ['required', 'in:course']
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()->id
        ]);
    }

}
