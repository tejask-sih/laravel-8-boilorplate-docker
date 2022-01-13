<?php

namespace App\Services\V1;
use App\Services\BaseService;
use App\Repositories\V1\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService extends BaseService
{
	/**
	 * @var RoleRepository
	 */
	private $roleRepository;

	/**
	 * @var Importer
	 */

	/**
	 * RoleRepository constructor.
	 * @param RoleRepository $roleRepository
	 * @param Importer $importer
	 */
	public function __construct(RoleRepository $roleRepository)
	{

		$this->roleRepository = $roleRepository;
	}
	/* List Data in Role */

	public function list(Request $request, int $page, int $perPage, string $filter = null)
	{
		$paginationOptions = $this->paginationOptions($page, $perPage);
		$list = $this->roleRepository->list(
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
	/* Store Data in Users Roles */
    public function create($attributes)
	{
        return $this->roleRepository->create($attributes);
	}
	/* Read Data in Users Role */
	public function read($id)
	{
        return $this->roleRepository->find($id);
	}
	/* Update Data in Users role */
	public function update($attributes,$id)
	{
	    return $this->roleRepository->update($id, $attributes);
	}
	/* Delete Data in Users Roles */
	public function delete($id)
	{
        return $this->roleRepository->delete($id);
	}

}
