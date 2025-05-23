<?php

namespace App\Http\Requests\Salons\SalonMenuRequest;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'nullable|in:approved,rejected',
            'admin_note' => 'nullable|string',
        ];
    }
}
