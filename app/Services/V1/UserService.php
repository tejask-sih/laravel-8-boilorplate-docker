<?php

namespace App\Services\V1;
use App\Services\BaseService;
use App\Repositories\V1\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService extends BaseService
{
	/**
	 * @var TransactionModelTypeRepository
	 */
	private $userRepository;

	/**
	 * @var Importer
	 */

	/**
	 * UserRepository constructor.
	 * @param UserRepository $userRepository
	 * @param Importer $importer
	 */
	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/* List Data in Users */
	public function list(Request $request, int $page, int $perPage, string $filter = null)
	{
		if($perPage == 0){
			$perPage = 10000;
		}
		$paginationOptions = $this->paginationOptions($page, $perPage);
		$list = $this->userRepository->list(
			1,
			$paginationOptions['page'],
			$paginationOptions['per_page'],
			$filter,
			$request

		);

		return new LengthAwarePaginator(
			$list['data'],
			$list['count'],
			$paginationOptions['per_page'],
			$paginationOptions['page'],
			['path' => $request->url(), 'query' => $request->query()]
		);
	}

	/* Store Data in Users */
    public function create($attributes)
	{
        return $this->userRepository->create($attributes);
	}

	/* Read Data in Users */
	public function read($id)
	{
        return $this->userRepository->find($id);
	}

	/* Update Data in Users */
	public function update($attributes,$id)
	{
	    return $this->userRepository->update($id, $attributes);
	}

	/* Delete Data in Users */
	public function delete($id,Request $request)
	{
	    $attributes = $request->all();
        return $this->userRepository->delete($id, $attributes);
	}
}