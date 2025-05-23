<?php

namespace App\Http\Services\Salons;

use App\Http\Permissions\Salons\SalonPermission;
use App\Http\Resources\Services\GroupResource;
use App\Models\Salons\Salon;
use App\Models\Salons\SalonPermission as SalonPermissionModel;
use App\Models\Salons\UserSalonPermission;
use App\Models\Services\Group;
use App\Models\Users\User;
use App\Services\FilterService;
use App\Services\ImageService;
use App\Services\MessageService;
use App\Services\PhoneService;

class SalonService
{
    public function getPermissions()
    {
        $user = User::auth();

        $userPermissions = UserSalonPermission::where('user_id', $user->id)
            ->with('permission')
            ->get();

        $permissions = SalonPermissionModel::whereIn(
            'id',
            $userPermissions->pluck('permission_id')
        )->get();


        // orders by filed `orders` in salon_permissions table
        $permissions = $permissions->sortBy(function ($permission) {
            return $permission->order;
        });

        return $permissions;
    }

    //getSalonData
    public function getSalonData()
    {
        $user = User::auth();

        $salon = $user->salon;

        $salon->load([
            'socialMediaSites',
            'images',
            'workingHours',
            'owner',
            'latestReviews',
            'loyaltyService',
        ]);

        return $salon;
    }

    public function index($data)
    {
        $query = Salon::query()->with(['loyaltyService', 'owner', 'images']);

        $searchFields = [
            'name',
            'email',
            'description',
            'location',
            'city',
            'country',
            'merchant_legal_name',
            'merchant_commercial_name',
            'address',
            'city_street_name',
            'contact_name',
            'contact_email',
            'business_contact_name',
            'business_contact_email',
            'bio',
            'tags',
        ];
        $numericFields = [];
        $dateFields = ['created_at'];
        $exactMatchFields = ['is_active', 'is_approved', 'type', 'city', 'country'];
        $inFields = ['id', 'type'];

        $query = SalonPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            $searchFields,
            $numericFields,
            $dateFields,
            $exactMatchFields,
            $inFields
        );
    }

    public function show($id)
    {
        $salon = Salon::where('id', $id)->first();

        if (!$salon) {
            MessageService::abort(404, 'messages.salon.item_not_found');
        }


        $salon->load(['socialMediaSites', 'images', 'workingHours', 'owner', 'latestReviews', 'loyaltyService']);

        return $salon;
    }

    public function create($data)
    {

        $salon = Salon::create($data);

        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $image) {

                $salon->images()->create(
                    [
                        'path' => $image,
                        'type' => 'salon_cover',
                    ]
                );
            }
        }


        $salon->load(['socialMediaSites', 'images', 'workingHours', 'owner', 'latestReviews', 'loyaltyService']);

        return $salon;
    }

    public function update($salon, $data)
    {

        // if (isset($data['phone'])) {
        //     $phoneParts = PhoneService::parsePhoneParts($data['phone']);

        //     $data['phone_code'] = $phoneParts['country_code'];
        //     $data['phone'] = $phoneParts['national_number'];
        // }


        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $image) {

                $salon->images()->create(
                    [
                        'path' => $image,
                        'type' => 'salon_cover',
                    ]
                );
            }
        }

        // images_remove
        if (isset($data['images_remove']) && is_array($data['images_remove'])) {
            ImageService::removeImages($data['images_remove']);
        }

        $salon->update(
            [
                'merchant_legal_name' => $data['merchant_legal_name'] ?? $salon->merchant_legal_name,
                'merchant_commercial_name' => $data['merchant_commercial_name'] ?? $salon->merchant_commercial_name,
                'address' => $data['address'] ?? $salon->address,
                'city_street_name' => $data['city_street_name'] ?? $salon->city_street_name,
                'contact_name' => $data['contact_name'] ?? $salon->contact_name,
                'contact_number' => $data['contact_number'] ?? $salon->contact_number,
                'contact_email' => $data['contact_email'] ?? $salon->contact_email,
                'business_contact_name' => $data['business_contact_name'] ?? $salon->business_contact_name,
                'business_contact_email' => $data['business_contact_email'] ?? $salon->business_contact_email,
                'business_contact_number' => $data['business_contact_number'] ?? $salon->business_contact_number,

                'icon' => $data['icon'] ?? $salon->icon,
                'description' => $data['description'] ?? $salon->description,
                'latitude' => $data['latitude'] ?? $salon->latitude,
                'longitude' => $data['longitude'] ?? $salon->longitude,
                'types' => !isset($data['types']) ? $salon->types : implode(',', $data['types']),
                'bio' => $data['bio'] ?? $salon->bio,

                'is_active' => $data['is_active'] ?? $salon->is_active,
                'is_approved' => $data['is_approved'] ?? $salon->is_approved,
                'block_message' => $data['block_message'] ?? $salon->block_message,
                'tags' => $data['tags'] ?? $salon->tags,
                'loyalty_service_id' => $data['loyalty_service_id'] ?? $salon->loyalty_service_id,



                // old data
                'name' => '',
                'phone_code' => '',
                'phone' => '',
                'email' => null,
                'location' => '',
                'type' => 'salon',
                'country' => '',
                'city' => '',
            ]
        );

        $salon->load(['socialMediaSites', 'images', 'workingHours', 'owner', 'latestReviews', 'loyaltyService']);

        return $salon;
    }

    public function destroy($salon)
    {
        return $salon->delete();
    }
}
