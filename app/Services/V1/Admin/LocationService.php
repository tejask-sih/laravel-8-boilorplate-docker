<?php 
namespace App\Services\V1\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Location;
use App\Repositories\V1\Admin\LocationRepository;
use Exception;
use InvalidArgumentException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\BaseService;

class LocationService extends BaseService
{
	/**
	 * @var locationRepository
	 */
	private $locationRepository;

	/**
	 * @var Importer
	 */

	/**
	 * ActionType constructor.
	 * @param locationRepository $locationRepository
	 * @param Importer $importer
	 */
	public function __construct(LocationRepository $locationRepository)
	{

		$this->locationRepository = $locationRepository ;
    }
    
	/**
	 * Listing Locations
	*/
	public function list(Request $request, int $page, int $perPage, string $filter = null)
	{
		$paginationOptions = $this->paginationOptions($page, $perPage);
		$list = $this->locationRepository->list(
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
		return $this->locationRepository->all();
    }
    
	/**
	* Insert Data into Locations with request Data
	* @param array 
	*/
    public function create( array $attributes )
	{
	    return $this->locationRepository->create($attributes);
	}
	
	/**
    * Read Locations with specified id
    * @param $id
	*/
	public function read($id)
	{
        return $this->locationRepository->find($id);
    }
    
	/**
	* Update Locations with specified id
	*/
	public function update( array $attributes, $id)
	{
	  	return $this->locationRepository->update($id, $attributes);
    }

    /**
	* Update Locations with specified id
	*/
	public function activate( array $attributes,$id)
	{
	  	return $this->locationRepository->activate($id, $attributes);
    }

    /**
	* Update Locations with specified id
	*/
	public function deactivate( array $attributes,$id)
	{
	  	return $this->locationRepository->deactivate($id, $attributes);
    }
    
	/**
	 * Delete Locations with specified id
	 *
	*/
	public function delete($id)
	{
      return $this->locationRepository->delete($id);
	}

	/**
	* LOV action type 
	*/
	public function getLocationList($id="",$fields="")
	{
		if(empty($fields))
		{
			$fields = ['*'];
		}
		if(!empty($id))
		{
			return $this->locationRepository->getLocation($id,$fields);
		}else{
			return $this->locationRepository->getLocationList($fields);
		}
	}




}