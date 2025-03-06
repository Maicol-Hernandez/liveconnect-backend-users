<?php

namespace App\Models;

class Like
{
    private User $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
