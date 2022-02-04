<?php
namespace App\Repositories\V1\Admin;

//use App\Library\FunctionUtils;
use App\Traits\CommonTrait;
use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
	use CommonTrait;
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

}