<?php 
namespace App\Repositories\V1\Admin;

use App\Models\Admin\Location;
use Auth;
use Carbon\Carbon;
use App\Repositories\BaseRepository;
use App\Traits\CommonTrait;

class LocationRepository extends BaseRepository
{
	use CommonTrait;
	/**
	* @var states
	*/
	protected $location;

	/**
	* Location constructor.
	* @param Location $location
	*/
	public function __construct(Location $location)
	{
		$this->location = $location;
	}

	/**
	* Listing location with Search Functionality.
	*/
	public function list($request, int $page, int $perPage, string $filter = null): array
	{
		// $location_list = $this->location->where('status',1)->get();
		// //$location_list = $this->location->all();
		// return $location_list;
		$model = $this->location;
		$data = $model;

		$data = $data
        ->where('status', 1)
		->forPage($page, $perPage);
		
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
	* Insert Data into location with request Data
	*/
	public function create($attributes)
	{
		$attributes['updated_at'] = date("Y-m-d H:i:s");
		return $this->location->create($attributes);
		
    }
    
	/**
	* listing location
	*/
	public function all()
	{
	    return $this->location->all();
    }
    
	/**
	* Find location data with given id
	*/
	public function find($id)
	{
	    return $this->location->find($id);
    }
    
	/**
	* Update location data with given id
	*/
	public function update($id, array $attributes)
	{
		$attributes['updated_at'] = date("Y-m-d H:i:s");	
		return $this->location->find($id)->update($attributes);		
	}

	/**
	* Update location data with given id
	*/
	public function activate($id, array $attributes)
	{
		$attributes['status'] = 1;	
		return $this->location->find($id)->update($attributes);		
	}

	/**
	* Update location data with given id
	*/
	public function deactivate($id, array $attributes)
	{
		$attributes['status'] = 0;	
		return $this->location->find($id)->update($attributes);		
	}

	/**
	* Delete location data with given id
	*/
	public function delete($id)
	{
		return $this->location->find($id)->delete();
	}
    /**
	* LOV action type specific data get
	*/
	public function getLocation($id="",$fields)
	{        
        if(!empty($id))
        {
            return $this->location->where('id',$id)->orderBy('id','desc')->get($fields);   
        }else{
            return $this->location->orderBy('id','desc')->get($fields); 
        }     
	}

    /**
	* LOV action type all data get
	*/
	public function getLocationList($fields)
	{ 
        return $this->location->orderBy('id','desc')->get($fields); 
	}
}