<?php 

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function getLoggedUserAccounts(): Collection
    {
        return Auth::user()->accounts()->select(['id', 'title', 'description'])->get();
    }
}