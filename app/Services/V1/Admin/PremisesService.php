<?php 
namespace App\Services\V1\Admin;

use Illuminate\Http\Request;
use App\Repositories\V1\Admin\PremisesRepository;
use Exception;
use InvalidArgumentException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\BaseService;

class PremisesService extends BaseService
{
	/**
	 * @var actionRepository
	 */
	private $premisesRepository;

	/**
	 * @var Importer
	 */

	/**
	 * ActionType constructor.
	 * @param actionTypeRepository $actionTypeRepository
	 * @param Importer $importer
	 */
	public function __construct(PremisesRepository $premisesRepository)
	{

		$this->premisesRepository = $premisesRepository ;
    }
    
	/**
	 * Listing Action Type Data
	 *
	*/
	public function list(Request $request, int $page, int $perPage, string $filter = null)
	{
		$paginationOptions = $this->paginationOptions($page, $perPage);
		$list = $this->premisesRepository->list(
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
		return $this->premisesRepository->all();
    }
    
	/**
	* Insert Data into action type with request Data
	* @param array 
	*/
    public function create( array $attributes )
	{
	    return $this->premisesRepository->create($attributes);
	}
	
	/**
    * Read action type with specified id
    * @param $id
	*/
	public function read($id)
	{
        return $this->premisesRepository->find($id);
    }
    
	/**
	* Update action type with specified id
	*/
	public function update( array $attributes, $id)
	{
	  	return $this->premisesRepository->update($id, $attributes);
    }

    /**
	* Update action type with specified id
	*/
	public function activate( array $attributes,$id)
	{
	  	return $this->premisesRepository->activate($id, $attributes);
    }

    /**
	* Update action type with specified id
	*/
	public function deactivate( array $attributes,$id)
	{
	  	return $this->premisesRepository->deactivate($id, $attributes);
    }
    
	/**
	 * Delete action type with specified id
	 *
	*/
	public function delete($id)
	{
      return $this->premisesRepository->delete($id);
	}	
	/**
	* LOV action type 
	*/
	public function lov($id,$fields="")
	{
		if(empty($fields))
		{
			$fields = ['*'];
		}
		return $this->premisesRepository->lov($id,$fields);
	}

}