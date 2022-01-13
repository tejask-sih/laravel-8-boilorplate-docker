<?php 
namespace App\Repositories\V1\Admin;

use App\Models\Admin\States;
use App\Models\Admin\Cities;
use Auth;
use Carbon\Carbon;
use App\Repositories\BaseRepository;
use App\Traits\CommonTrait;

class StatesRepository extends BaseRepository
{
	use CommonTrait;
	/**
	* @var states
	*/
	protected $states;

	/**
	* actionTypeRepository constructor.
	* @param actionType $actionType
	*/
	public function __construct(States $states)
	{
		$this->states = $states;
	}

	/**
	* Listing actionType with Search Functionality.
	*/
	public function list($request, int $page, int $perPage, string $filter = null): array
	{		
		// $state_list = $this->states->all();
		// foreach ($state_list as $state) {
		// 	$cities = Cities::where(['state_id' => $state->id, 'status' => 1]);
		// 	$data = $cities->get()->toArray();
		// 	$city_count = count($data);
		//     $state['cities'] = $city_count;
		// }
		// dd($state_list);
		// return $state_list;

		$model = $this->states;
		$data = $model;

		$data = $data
        ->where('status', 1)
		->forPage($page, $perPage)
		->withCount(array('cities'));
		
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
	* Insert Data into action type with request Data
	*/
	public function create($attributes)
	{
		$attributes['updated_at'] = date("Y-m-d H:i:s");
		$attributes['deleted_at'] = date("Y-m-d H:i:s");
		return $this->states->create($attributes);
    }
    
	/**
	* listing action type
	*/
	public function all()
	{		
	    return $this->states->all();
    }
    
	/**
	* Find action type data with given id
	*/
	public function find($id)
	{
	    return $this->states->find($id);
    }
    
	/**
	* Update action type data with given id
	*/
	public function update($id, array $attributes)
	{
		$attributes['updated_at'] = date("Y-m-d H:i:s");	
		return $this->states->find($id)->update($attributes);		
	}

	/**
	* Update action type data with given id
	*/
	public function activate($id, array $attributes)
	{
		$attributes['status'] = 1;	
		return $this->states->find($id)->update($attributes);		
	}

	/**
	* Update action type data with given id
	*/
	public function deactivate($id, array $attributes)
	{
		$attributes['status'] = 0;	
		return $this->states->find($id)->update($attributes);		
	}

	/**
	* Delete action type data with given id
	*/
	public function delete($id)
	{
		return $this->states->find($id)->delete();
	}
	/**
	* LOV action type 
	*/
	public function getStateList($fields)
	{
        return $this->states->orderBy('id','desc')->get($fields);        
	}
}