<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $permission = $this->route('permission');
        $id = $permission instanceof \Spatie\Permission\Models\Permission ? $permission->id : $permission;

        return [
            'name' => 'sometimes|string|unique:permissions,name,' . $id,
        ];
    }
}
