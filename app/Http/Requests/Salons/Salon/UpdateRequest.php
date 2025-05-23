<?php

namespace App\Http\Requests\Salons\Salon;

use App\Http\Requests\BaseFormRequest;
use App\Models\Salons\Salon;
use App\Models\Users\User;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {

        $rules = [
            'merchant_legal_name'       => 'nullable|string|max:255',
            'merchant_commercial_name'  => 'nullable|string|max:255',
            'address'                   => 'nullable|string|max:255',
            'city_street_name'          => 'nullable|string|max:255',
            'contact_name'              => 'nullable|string|max:255',
            'contact_number'            => 'nullable|string|max:25',
            'contact_email'             => 'nullable|email|max:255',
            'business_contact_name'     => 'nullable|string|max:255',
            'business_contact_email'    => 'nullable|email|max:255',
            'business_contact_number'   => 'nullable|string|max:25',
            'bio'                       => 'nullable|string',
            'latitude'                  => 'nullable|numeric',
            'longitude'                 => 'nullable|numeric',
            'name'                      => 'nullable|string|max:255',
            'icon'                      => 'nullable|string',
            'phone_code'                => 'nullable|string|max:7',
            'phone'                     => 'nullable|string|max:12',
            'email'                     => 'nullable|email',
            'description'               => 'nullable|string',
            'location'                  => 'nullable|string|max:255',
            'types' => [
                'nullable',
                'array',
                'distinct',
                function ($attribute, $value, $fail) {

                    $id = request()->route('id');

                    $salon = Salon::find($id);
                    if (!$salon) {
                        $fail("The salon with ID {$id} does not exist.");
                        return;
                    }

                    $type = $salon->type;

                    $allowedTypes = match ($type) {
                        'salon' => ['home_service', 'beautician'],
                        'clinic' => ['home_service'],
                        default => [],
                    };

                    if (!empty($value) && array_diff($value, $allowedTypes)) {
                        $fail("The selected {$attribute} is invalid for the type {$type}.");
                    }
                },
            ],
            'types.*' => 'required|string|in:salon,home_service,beautician,clinic',
            'country'                   => 'nullable|string|max:255',
            'city'                      => 'nullable|string|max:255',
            'tags'                      => 'nullable|string',
            'loyalty_service_id'        => 'nullable|integer|exists:services,id,deleted_at,NULL',

            'images' => 'nullable|array',
            'images.*' => 'nullable|string|max:255',
            'images_remove' => 'nullable|array',
            'images_remove.*' => 'nullable|integer|exists:images,id,deleted_at,NULL',
        ];

        $user = User::auth();

        $rules_admin = [];

        if ($user->isAdmin()) {
            $rules_admin = [
                'is_approved'               => 'nullable|boolean',
                'is_active'                 => 'nullable|boolean',
                'block_message'             => 'nullable|string|max:255',
            ];
        }

        // merge rules
        $rules = array_merge($rules, $rules_admin);

        return $rules;
    }
}
