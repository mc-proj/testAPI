<?php
namespace App\Controller;
use App\Entity\User;

class SetUserOnlineController {
    public function __invoke(User $data): User {
        $data->setOnline(true);
        return $data;
    }
}