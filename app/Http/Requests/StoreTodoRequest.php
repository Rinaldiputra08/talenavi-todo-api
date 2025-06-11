<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTodoRequest extends FormRequest
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

        return [
            'title' => ['required', 'string', 'max:255'],
            'assignee' => ['nullable', 'string', 'max:255'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'time_tracked' => ['numeric', 'min:0', 'nullable'],
            'status' => ['nullable', Rule::in(['pending', 'open', 'in_progress', 'completed'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
        ];
    }

    protected function prepareForValidation()
    {
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'pending',
            ]);
        }

        if (!$this->has('time_tracked') || is_null($this->input('time_tracked'))) {
            $this->merge([
                'time_tracked' => 0,
            ]);
        }
    }
}
