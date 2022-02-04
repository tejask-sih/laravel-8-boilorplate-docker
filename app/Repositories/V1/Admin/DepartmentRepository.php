<?php 
namespace App\Repositories\V1\Admin;

use App\Models\Admin\Departments;
use Auth;
use Carbon\Carbon;
use App\Repositories\BaseRepository;
use App\Traits\CommonTrait;

class DepartmentRepository extends BaseRepository
{
	use CommonTrait;
	/**
	* @var states
	*/
	protected $departments;

	/**
	* actionTypeRepository constructor.
	* @param actionType $actionType
	*/
	public function __construct(Departments $departments)
	{
		$this->departments = $departments;
	}

	/**
	* Listing actionType with Search Functionality.
	*/
	public function list($request, int $page, int $perPage, string $filter = null): array
	{		
        $model = $this->departments;
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
	* Insert Data into action type with request Data
	*/
	public function create($attributes)
	{
		$attributes['updated_at'] = date("Y-m-d H:i:s");
		$attributes['created_at'] = date("Y-m-d H:i:s");
		return $this->departments->create($attributes);
    }
    
	/**
	* listing action type
	*/
	public function all()
	{
	    return $this->departments->all();
    }
    
	/**
	* Find action type data with given id
	*/
	public function find($id)
	{
	    return $this->departments->find($id);
    }
    
	/**
	* Update action type data with given id
	*/
	public function update($id, array $attributes)
	{
		$attributes['updated_at'] = date("Y-m-d H:i:s");	
		return $this->departments->find($id)->update($attributes);		
	}

	/**
	* Update action type data with given id
	*/
	public function activate($id, array $attributes)
	{
		$attributes['status'] = 1;	
		return $this->departments->find($id)->update($attributes);		
	}

	/**
	* Update action type data with given id
	*/
	public function deactivate($id, array $attributes)
	{
		$attributes['status'] = 0;	
		return $this->departments->find($id)->update($attributes);		
	}

	/**
	* Delete action type data with given id
	*/
	public function delete($id)
	{
		return $this->departments->find($id)->delete();
	}

	/**
	* LOV action type 
	*/
	public function lov($fields)
	{
        return $this->departments->orderBy('id','desc')->get($fields);        
	}
}