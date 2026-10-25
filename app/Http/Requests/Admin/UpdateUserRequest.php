<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $id = $this->route('user')?->id;
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at')],
            'phone' => ['required', 'digits_between:6,15', Rule::unique('users', 'phone')->ignore($id)->whereNull('deleted_at')],
            'country_code' => ['nullable', 'string', 'max:8'],
            'status' => ['required', 'boolean'],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => (string) $this->phone,
            'status' => (int) $this->status,
            'email' => is_string($this->email) ? strtolower(trim($this->email)) : $this->email,
        ]);
    }
}
