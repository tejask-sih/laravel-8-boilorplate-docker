<?php 
namespace App\Services\V1\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\InquiryArea;
use App\Repositories\V1\Admin\InquiryAreaRepository;
use Exception;
use InvalidArgumentException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\BaseService;

class InquieryAreaService extends BaseService
{
	/**
	 * @var InquiryAreaRepository
	 */
	private $inquieryAreaRepository;

	/**
	 * @var Importer
	 */

	/**
	 * ActionType constructor.
	 * @param inquieryAreaRepository $locationRepository
	 * @param Importer $importer
	 */
	public function __construct(InquiryAreaRepository $inquieryAreaRepository)
	{

		$this->inquieryAreaRepository = $inquieryAreaRepository ;
    }
    
	/**
	 * Listing Locations
	*/
	public function list(Request $request, int $page, int $perPage, string $filter = null)
	{
		$paginationOptions = $this->paginationOptions($page, $perPage);
		$list = $this->inquieryAreaRepository->list(
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
		return $this->inquieryAreaRepository->all();
    }
    
	/**
	* Insert Data into Locations with request Data
	* @param array 
	*/
    public function create( array $attributes )
	{
	    return $this->inquieryAreaRepository->create($attributes);
	}
	
	/**
    * Read Locations with specified id
    * @param $id
	*/
	public function read($id)
	{
        return $this->inquieryAreaRepository->find($id);
    }
    
	/**
	* Update Locations with specified id
	*/
	public function update( array $attributes, $id)
	{
	  	return $this->inquieryAreaRepository->update($id, $attributes);
    }

    /**
	* Update Locations with specified id
	*/
	public function activate($attributes,$id)
	{
	  	return $this->inquieryAreaRepository->activate($id, $attributes);
    }

    /**
	* Update Locations with specified id
	*/
	public function deactivate($attributes,$id)
	{
	  	return $this->inquieryAreaRepository->deactivate($id, $attributes);
    }
    
	/**
	 * Delete Locations with specified id
	 *
	*/
	public function delete($id)
	{
      return $this->inquieryAreaRepository->delete($id);
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
		return $this->inquieryAreaRepository->lov($id,$fields);
	}

}