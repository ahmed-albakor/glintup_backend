<?php

// CreateRequest.php
namespace App\Http\Requests\Salons\SalonCustomer;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $rules = [
            'user_id'   => 'required|exists:users,id',
            'is_banned' => 'nullable|boolean',
            'notes'     => 'nullable|string|max:1000',
        ];

        $user = User::auth();

        if ($user->isAdmin()) {
            $rules['salon_id'] = 'required|exists:salons,id,deleted_at,NULL';
        }

        return $rules;
    }
}
