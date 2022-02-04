<?php 
namespace App\Services\V1\Admin;

use Illuminate\Http\Request;
use App\Repositories\V1\Admin\PremisesTypeRepository;
use Exception;
use InvalidArgumentException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\BaseService;

class PremisesTypeService extends BaseService
{
	/**
	 * @var actionTypeRepository
	 */
	private $premisesTypeRepository;

	/**
	 * @var Importer
	 */

	/**
	 * ActionType constructor.
	 * @param actionTypeRepository $actionTypeRepository
	 * @param Importer $importer
	 */
	public function __construct(PremisesTypeRepository $premisesTypeRepository)
	{

		$this->premisesTypeRepository = $premisesTypeRepository ;
    }
    
	/**
	 * Listing Action Type Data
	 *
	*/
	public function list(Request $request, int $page, int $perPage, string $filter = null)
	{
		$paginationOptions = $this->paginationOptions($page, $perPage);
		$list = $this->premisesTypeRepository->list(
			$request,
			$paginationOptions['page'],
			$paginationOptions['per_page'],
			$filter
		);

		return new LengthAwarePaginator(
			$list['data'],
			$list['count'],
			$paginationOptions['per_page'],
			$paginationOptions['page'],
			['path' => $request->url(), 'query' => $request->query()]
		);
	}
    
	/**
    * listing action type
	*/
	public function index()
	{
		return $this->premisesTypeRepository->all();
    }
    
	/**
	* Insert Data into action type with request Data
	* @param array 
	*/
    public function create( array $attributes )
	{
	    return $this->premisesTypeRepository->create($attributes);
	}
	
	/**
    * Read action type with specified id
    * @param $id
	*/
	public function read($id)
	{
        return $this->premisesTypeRepository->find($id);
    }
    
	/**
	* Update action type with specified id
	*/
	public function update( array $attributes, $id)
	{
	  	return $this->premisesTypeRepository->update($id, $attributes);
    }

    /**
	* Update action type with specified id
	*/
	public function activate( array $attributes,$id)
	{
	  	return $this->premisesTypeRepository->activate($id, $attributes);
    }

    /**
	* Update action type with specified id
	*/
	public function deactivate( array $attributes,$id)
	{
	  	return $this->premisesTypeRepository->deactivate($id, $attributes);
    }
    
	/**
	 * Delete action type with specified id
	 *
	*/
	public function delete($id)
	{
      return $this->premisesTypeRepository->delete($id);
	}
	/**
	* LOV action type 
	*/
	public function lov($fields ="")
	{
		if(empty($fields))
		{
			$fields = ['*'];
		}
		return $this->premisesTypeRepository->lov($fields);
    }

}