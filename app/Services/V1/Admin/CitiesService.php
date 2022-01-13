<?php 
namespace App\Services\V1\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\States;
use App\Repositories\V1\Admin\CitiesRepository;
use Exception;
use InvalidArgumentException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\BaseService;

class CitiesService extends BaseService
{
	/**
	 * @var actionTypeRepository
	 */
	private $citiesRepository;

	/**
	 * @var Importer
	 */

	/**
	 * ActionType constructor.
	 * @param actionTypeRepository $actionTypeRepository
	 * @param Importer $importer
	 */
	public function __construct(CitiesRepository $citiesRepository)
	{

		$this->citiesRepository = $citiesRepository ;
    }
    
	/**
	 * Listing Action Type Data
	 *
	*/
	public function list(Request $request, int $page, int $perPage, string $filter = null)
	{
		$paginationOptions = $this->paginationOptions($page, $perPage);
		$list = $this->citiesRepository->list(
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
		return $this->citiesRepository->all();
    }
    
	/**
	* Insert Data into action type with request Data
	* @param array 
	*/
    public function create( array $attributes )
	{
		
	    return $this->citiesRepository->create($attributes);
	}
	
	/**
    * Read action type with specified id
    * @param $id
	*/
	public function read($id)
	{
        return $this->citiesRepository->find($id);
    }
    
	/**
	* Update action type with specified id
	*/
	public function update( array $attributes, $id)
	{
	  	return $this->citiesRepository->update($id, $attributes);
    }

    /**
	* Update action type with specified id
	*/
	public function activate( array $attributes,$id)
	{
	  	return $this->citiesRepository->activate($id, $attributes);
    }

    /**
	* Update action type with specified id
	*/
	public function deactivate( array $attributes,$id)
	{
	  	return $this->citiesRepository->deactivate($id, $attributes);
    }
    
	/**
	 * Delete action type with specified id
	 *
	*/
	public function delete($id)
	{
      return $this->citiesRepository->delete($id);
	}

	/**
	* LOV action type 
	*/
	public function getCityList($state_id,$fields="")
	{
		if(empty($fields))
		{
			$fields = ['*'];
		}
		return $this->citiesRepository->getCityList($state_id,$fields);
	}

}