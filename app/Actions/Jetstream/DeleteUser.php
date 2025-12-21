<?php

namespace App\Actions\Jetstream;

use App\Domain\Admin\Models\Admin;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    public function delete(Admin $user): void
    {
        $user->tokens->each->delete();
        $user->delete();
    }
}
