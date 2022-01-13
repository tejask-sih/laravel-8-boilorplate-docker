<?php 
namespace App\Services\V1\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Location;
use App\Repositories\V1\Admin\AreaRepository;
use Exception;
use InvalidArgumentException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\BaseService;

class AreaService extends BaseService
{
	/**
	 * @var locationRepository
	 */
	private $areaRepository;

	/**
	 * @var Importer
	 */

	/**
	 * ActionType constructor.
	 * @param locationRepository $locationRepository
	 * @param Importer $importer
	 */
	public function __construct(AreaRepository $areaRepository)
	{

		$this->areaRepository = $areaRepository ;
    }
    
	/**
	 * Listing Areas
	*/
    public function list(Request $request, int $page, int $perPage, string $filter = null)
	{
		$paginationOptions = $this->paginationOptions($page, $perPage);
		$list = $this->areaRepository->list(
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
    * listing Locations
	*/
	public function index()
	{
		return $this->areaRepository->all();
    }
    
	/**
	* Insert Data into Locations with request Data
	* @param array 
	*/
    public function create( array $attributes )
	{
	    return $this->areaRepository->create($attributes);
	}
	
	/**
    * Read Locations with specified id
    * @param $id
	*/
	public function read($id)
	{
        return $this->areaRepository->find($id);
    }
    
	/**
	* Update Locations with specified id
	*/
	public function update( array $attributes, $id)
	{
	  	return $this->areaRepository->update($id, $attributes);
    }

    /**
	* Update Locations with specified id
	*/
	public function activate( array $attributes,$id)
	{
	  	return $this->areaRepository->activate($id, $attributes);
    }

    /**
	* Update Locations with specified id
	*/
	public function deactivate( array $attributes,$id)
	{
	  	return $this->areaRepository->deactivate($id, $attributes);
    }
    
	/**
	 * Delete Locations with specified id
	 *
	*/
	public function delete($id)
	{
      return $this->areaRepository->delete($id);
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
		return $this->areaRepository->lov($id,$fields);
	}

}