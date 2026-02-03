<?php
namespace App\Traits;

use App\Models\Role;
use App\User;
use Illuminate\Support\Facades\Auth;

trait DeterminesUserType
{
    public function getUserType($userId)
    {
        $user = User::find($userId);

        if ($user) {
            $role = $user->roles()->first();
            return $role ? $role->name : null;
        }

        return null;
    }
}
