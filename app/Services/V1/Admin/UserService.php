<?php

namespace App\Services\V1\Admin;

use App\Repositories\Admin\V1\UserRepository;
use App\Services\BaseService;

class UserService extends BaseService
{
	private $userRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }
}