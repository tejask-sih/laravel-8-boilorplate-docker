<?php 
namespace App\Repositories\V1\Admin;

use App\Models\Admin\Area;
use Auth;
use Carbon\Carbon;
use App\Repositories\BaseRepository;
use App\Traits\CommonTrait;

class AreaRepository extends BaseRepository
{
	use CommonTrait;
	/**
	* @var states
	*/
	protected $area;

	/**
	* Location constructor.
	* @param Location $location
	*/
	public function __construct(Area $area)
	{
		$this->area = $area;
	}

	/**
	* Listing location with Search Functionality.
	*/
	public function list($request, int $page, int $perPage, string $filter = null): array
	{
		// $area_list = $this->area->leftjoin('mst_states', 'mst_areas.state_id' , '=', 'mst_states.id')
  //       ->leftjoin('mst_cities', 'mst_areas.city_id', '=',  'mst_cities.id')
  //       ->where('mst_areas.status', 1)
		// ->select('mst_areas.*','mst_states.name as State_name','mst_cities.name as City_name')
  //       ->get();

  //       dd($area_list);
        $model = $this->area;
		$data = $model;

		$data = $data
        ->where('status', 1)
        ->whereNull('location_id')
		->forPage($page, $perPage)
		->with(array('state' => function($query){
			$query->select('id','name');
		}))
		->with(array('city'=>function($query){
			$query->select('id','name');
		}));
		
		$data->orderBy('updated_at', 'desc')
		->orderBy('created_at', 'desc')
		->orderBy('id', 'desc');
		$data = $data->get()->toArray();
		$count = count($data);
		return ['count' => $count, 'data' => $data];
	}
	
	/**
	 * added extra key from # value in list
	 */
	private function modifyList($lists)
	{
		foreach($lists as $key => $list)
		{
			$lists[$key]['updated_at'] =  date('Y-m-d H:i:s',strtotime($list['updated_at']));
			$lists[$key]['created_at'] =  date('Y-m-d H:i:s',strtotime($list['created_at']));
		}
		return $lists;
	}
    
	/**
	* Insert Data into area with request Data
	*/
	public function create($attributes)
	{
		$attributes['updated_at'] = date("Y-m-d H:i:s");
		$attributes['deleted_at'] = date("Y-m-d H:i:s");
		return $this->area->create($attributes);
    }
    
	/**
	* listing area
	*/
	public function all()
	{
	    return $this->area->all();
    }
    
	/**
	* Find area data with given id
	*/
	public function find($id)
	{
	    return $this->area->find($id);
    }
    
	/**
	* Update area data with given id
	*/
	public function update($id, array $attributes)
	{
		$attributes['updated_at'] = date("Y-m-d H:i:s");	
		return $this->area->find($id)->update($attributes);		
	}

	/**
	* Update area data with given id
	*/
	public function activate($id, array $attributes)
	{
		$attributes['status'] = 1;	
		return $this->area->find($id)->update($attributes);		
	}

	/**
	* Update area data with given id
	*/
	public function deactivate($id, array $attributes)
	{
		$attributes['status'] = 0;	
		return $this->area->find($id)->update($attributes);		
	}

	/**
	* Delete area data with given id
	*/
	public function delete($id)
	{
		return $this->area->find($id)->delete();
	}

	/**
	* LOV action type 
	*/
	public function lov($id,$fields)
	{
		$area = $this->area;
		$data = $area;
		$data = $data
        ->where('id', $id)
		->with(['location' => function($query){
			$query->select('id','name');
		}])	
		->with(['state' => function($query){
			$query->select('id','name');
		}])	
		->with(['city' => function($query){
			$query->select('id','name');
		}]);			
		$data->orderBy('id', 'desc');		
        return $data->get($fields)->toArray();        
	}
}