<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\Group\CreateRequest;
use App\Http\Requests\Services\Group\UpdateRequest;
use App\Http\Resources\Services\GroupResource;
 use App\Http\Services\Services\GroupService;
use App\Http\Permissions\Services\GroupPermission;
use App\Services\ResponseService;

class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function index()
    {
        $groups = $this->groupService->index(request()->all());
        return response()->json([
            'success' => true,
            'data' => GroupResource::collection($groups->items()),
            'meta' => ResponseService::meta($groups),
        ]);
    }

    public function show($id)
    {
        $group = $this->groupService->show($id);
        GroupPermission::canShow($group);
        return response()->json([
            'success' => true,
            'data' => new GroupResource($group),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = GroupPermission::create($request->validated());

        $group = $this->groupService->create($data);
        return response()->json([
            'success' => true,
            'message' => trans('messages.group.item_created_successfully'),
            'data' => new GroupResource($group),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $group = $this->groupService->show($id);

        GroupPermission::canUpdate($group, $request->validated());
        
        $group = $this->groupService->update($group, $request->validated());
        return response()->json([
            'success' => true,
            'message' => trans('messages.group.item_updated_successfully'),
            'data' => new GroupResource($group),
        ]);
    }

    public function destroy($id)
    {
        $group = $this->groupService->show($id);
        GroupPermission::canDelete($group);
        $deleted = $this->groupService->destroy($group);
        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.group.item_deleted_successfully')
                : trans('messages.group.failed_delete_item'),
        ]);
    }
}
