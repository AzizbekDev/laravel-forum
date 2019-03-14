<?php 
namespace App\Filters;

use App\User;

class ThreadFilters extends Filters
{
    /**
     * Filter The Query by a given a username
     * @param string $username
     * @return mixed
     */
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();
        return $this->builder->where('user_id', $user->id);
    }
}