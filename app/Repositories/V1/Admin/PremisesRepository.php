<?php 
namespace App\Repositories\V1\Admin;

use App\Models\Admin\Premises;
use Auth;
use Carbon\Carbon;
use App\Repositories\BaseRepository;
use App\Traits\CommonTrait;

class PremisesRepository extends BaseRepository
{
	use CommonTrait;
	/**
	* @var states
	*/
	protected $states;

	/**
	* actionTypeRepository constructor.
	* @param action $action
	*/
	public function __construct(Premises $premises)
	{
		$this->premises = $premises;
	}

	/**
	* Listing action with Search Functionality.
	*/
	public function list($request, int $page, int $perPage, string $filter = null): array
	{
		// $premisesList = $this->premises->where('status',1)->get();
		// return $premisesList;
		$model = $this->premises;
		$data = $model;

		$data = $data
        ->where('status', 1)
		->forPage($page, $perPage)
		->with(['location' => function($query){
			$query->select('id','name');
		}])		
		->with(['premises_type' => function($query){
			$query->select('id','name');
		}])
		->with(['state' => function($query){
			$query->select('id','name');
		}])
		->with(['city'=>function($query){
			$query->select('id','name');
		}]);
		
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
		return $this->premises->create($attributes);
    }
    
	/**
	* listing premises
	*/
	public function all()
	{
	    return $this->premises->all();
    }
    
	/**
	* Find action type data with given id
	*/
	public function find($id)
	{
	    return $this->premises->find($id);
    }
    
	/**
	* Update action type data with given id
	*/
	public function update($id, array $attributes)
	{
		$attributes['updated_at'] = date("Y-m-d H:i:s");	
		return $this->premises->find($id)->update($attributes);		
	}

	/**
	* Update action type data with given id
	*/
	public function activate($id, array $attributes)
	{
		$attributes['status'] = 1;	
		return $this->premises->find($id)->update($attributes);		
	}

	/**
	* Update action type data with given id
	*/
	public function deactivate($id, array $attributes)
	{
		$attributes['status'] = 0;	
		return $this->premises->find($id)->update($attributes);		
	}

	/**
	* Delete action type data with given id
	*/
	public function delete($id)
	{
		return $this->premises->find($id)->delete();
	}
	/**
	* LOV action type 
	*/
	public function lov($id,$fields)
	{
		$premise = $this->premises;
		$data = $premise;
		$data = $data
        ->where('id', $id)
		->with(['location' => function($query){
			$query->select('id','name');
		}])		
		->with(['premises_type' => function($query){
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