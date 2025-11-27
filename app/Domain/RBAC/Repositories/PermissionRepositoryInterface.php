<?php

namespace App\Domain\RBAC\Repositories;

use App\Domain\RBAC\Models\Permission;
use Illuminate\Support\Collection;

interface PermissionRepositoryInterface
{
    public function findById(int $id): ?Permission;
    public function findByName(string $name): ?Permission;
    public function all(): Collection;
    public function getByResource(string $resource): Collection;
}
