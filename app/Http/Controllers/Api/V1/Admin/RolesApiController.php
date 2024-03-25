<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\Admin\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RolesApiController extends Controller
{
    public function index(): RoleResource
    {
        abort_if(Gate::denies('role_access'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return new RoleResource(Role::with(['permissions'])->advancedFilter());
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create($request->validated());
        $role->permissions()->sync($request->input('permissions.*.id', []));

        return (new RoleResource($role))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function create(): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('role_create'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [
                'permissions' => Permission::get([Permission::ID, Permission::TITLE]),
            ],
        ]);
    }

    public function show(Role $role): RoleResource
    {
        abort_if(Gate::denies('role_show'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return new RoleResource($role->load([Permission::TABLE_NAME]));
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role->update($request->validated());
        $role->permissions()->sync($request->input('permissions.*.id', []));

        return (new RoleResource($role))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function edit(Role $role): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('role_edit'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new RoleResource($role->load(['permissions'])),
            'meta' => [
                'permissions' => Permission::get(['id', 'title']),
            ],
        ]);
    }

    public function destroy(Role $role): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('role_delete'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        $role->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
