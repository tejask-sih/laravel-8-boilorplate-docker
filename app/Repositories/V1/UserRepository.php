<?php

namespace App\Repositories\V1;

use App\Repositories\BaseRepository;
use App\Models\User;
use App\Models\UserRoleMapping;
use App\Models\Role;
use Auth;

class UserRepository extends BaseRepository
{
	/**
	 * @var user
	 */
	private $user;

	/**
	 * User constructor.
	 * @param ReportData $User
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
		$this->isSuperAdmin    = Auth::check() ? Auth::user()->isSupperAdmin() : false;
		$this->isLedgerAdmin   = Auth::check() ? Auth::user()->isLedgerAdmin() : false;
		$this->defaultLedgerId = Auth::check() ? Auth::user()->getLogginUserLedger() : 1;
	}

	/**
	 * Create user for requested data 
	 */
	public function create($attributes)
	{
		$attributes['created_by'] = Auth::user()->id;
        $attributes['updated_by'] = Auth::user()->id;
		$attributes['created_at'] = date("Y-m-d H:i:s");
        $attributes['updated_at'] = date("Y-m-d H:i:s");
		return $this->user->create($attributes);
	}

	/**
	 * Listing with custom search with id, name,updated_at
	 */
	public function list(int $userId, int $page, int $perPage, string $filter = null, $request): array
	{
		$model = $this->user;
		$data = $model;
		$count = $data->count();
		$start = "";
		$end = "";
		$whr = "";
		if (!empty($request->input('filter_data'))) {
			foreach ($request->input('filter_data') as $key => $value) {
				if ($whr != '') {
					$whr .= ' AND ';
				}
				if ($key == "id" && $value['filter_type'] = "number") {
					if (is_int($value['filter']) == true) {
						if ($value['type']  == "equals") {
							$whr .= " $key = {$value['filter']} ";
						} else if ($value['type']  == "notEqual") {
							$whr .= " $key != {$value['filter']} ";
						} else if ($value['type']  == "lessThan") {
							$whr .= " $key < {$value['filter']} ";
						} else if ($value['type']  == "lessThanOrEqual") {
							$whr .= " $key <= {$value['filter']} ";
						} else if ($value['type']  == "greaterThan") {
							$whr .= " $key > {$value['filter']} ";
						} else if ($value['type']  == "greaterThanOrEqual") {
							$whr .= " $key >= {$value['filter']} ";
						} else if ($value['type']  == "inRange") {
							$whr .= " $key BETWEEN {$value['filter']} AND {$value['filter_to']} ";
						}
					} else {
						$whr .= " $key = 0 ";
					}
                }
                // first name is consider as a only name
                if ($key == "name") {
						
                    $value_strip_tag1 =  strip_tags($value['filter']);
                    $value_strip_tag =  str_replace('`', '', $value_strip_tag1);
                    $value_strip_tag =  str_replace("'", '', $value_strip_tag1);
                    if ($value['filter_type'] = "text" && $value['type']  == "contains") {
                        $whr .= " $key ilike '%{$value_strip_tag}%' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "notContains") {
                        $whr .= " $key NOT LIKE '%{$value_strip_tag}%' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "equals") {
                        $whr .= " $key = '{$value_strip_tag}' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "notEqual") {
                        $whr .= " $key != '{$value_strip_tag}' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "startsWith") {
                        $whr .= " $key ilike '{$value_strip_tag}%' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "endsWith") {
                        $whr .= " $key ilike '%{$value_strip_tag}' ";
                    }
                }
				if ($key == "last_name") {
						
                    $value_strip_tag1 =  strip_tags($value['filter']);
                    $value_strip_tag =  str_replace('`', '', $value_strip_tag1);
                    $value_strip_tag =  str_replace("'", '', $value_strip_tag1);
                    if ($value['filter_type'] = "text" && $value['type']  == "contains") {
                        $whr .= " $key ilike '%{$value_strip_tag}%' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "notContains") {
                        $whr .= " $key NOT LIKE '%{$value_strip_tag}%' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "equals") {
                        $whr .= " $key = '{$value_strip_tag}' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "notEqual") {
                        $whr .= " $key != '{$value_strip_tag}' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "startsWith") {
                        $whr .= " $key ilike '{$value_strip_tag}%' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "endsWith") {
                        $whr .= " $key ilike '%{$value_strip_tag}' ";
                    }
                }
                if ($key == "email") {
						
                    $value_strip_tag1 =  strip_tags($value['filter']);
                    $value_strip_tag =  str_replace('`', '', $value_strip_tag1);
                    $value_strip_tag =  str_replace("'", '', $value_strip_tag1);
                    if ($value['filter_type'] = "text" && $value['type']  == "contains") {
                        $whr .= " $key ilike '%{$value_strip_tag}%' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "notContains") {
                        $whr .= " $key NOT LIKE '%{$value_strip_tag}%' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "equals") {
                        $whr .= " $key = '{$value_strip_tag}' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "notEqual") {
                        $whr .= " $key != '{$value_strip_tag}' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "startsWith") {
                        $whr .= " $key ilike '{$value_strip_tag}%' ";
                    } else if ($value['filter_type'] = "text" && $value['type']  == "endsWith") {
                        $whr .= " $key ilike '%{$value_strip_tag}' ";
                    }
                }
				if ($key == "updated_at" && $value['filter_type'] = "date") {
                    $date_from = date('Y-m-d', strtotime($value['date_from']));
                    $date_to = date('Y-m-d', strtotime($value['date_to']));
                    if ($value['type']  == "equals") {
                        $whr .= " $key BETWEEN '{$date_from} 00:00:00' and '{$date_from} 23:59:59' ";
                    } else if ($value['type']  == "notEqual") {
                        $whr .= " $key NOT BETWEEN '{$date_from} 00:00:00' and '{$date_from} 23:59:59' ";
                    } else if ($value['type']  == "lessThan") {
                        $whr .= " $key < '{$date_from} 00:00:00' ";
                    } else if ($value['type']  == "greaterThan") {
                        $whr .= " $key > '{$date_from} 23:59:59' ";
                    } else if ($value['type']  == "inRange") {
                        $whr .= " $key BETWEEN '{$date_from} 00:00:00' AND '{$date_to} 23:59:59'";
                    }
                }
			}
		}
		$orderby = 'updated_at';
		$ordertype = 'desc';
		if (!empty($request->input('sort_data'))) {
			$orderby = $request->input('sort_data')[0]['col_id'];
			$ordertype = $request->input('sort_data')[0]['sort'];
		}
		
		if (isset($whr) && !empty($whr)) {
			$data = $data->WhereRaw($whr);
		}
		if(Auth::user()->id != 1) {
			//\DB::connection()->enableQueryLog();
			$data = User::select('users.*','users_roles_mapping.ledger_id','users_roles_mapping.user_id','users_roles_mapping.user_role_id','users_roles.role_name','ledgers.name')
			->forPage($page, $perPage)
			->withTrashed();
			$data = $data->join('users_roles_mapping', 'users_roles_mapping.user_id', '=', 'users.id');
			$data = $data->join('users_roles', 'users_roles.id', '=', 'users_roles_mapping.user_role_id');
			$data = $data->join('ledgers', 'ledgers.id', '=', 'users_roles_mapping.ledger_id');
			$data = $data->where([['users_roles_mapping.ledger_id',$this->defaultLedgerId],['users.created_by',Auth::user()->id]])
			->OrWhere([['users.id', '=' , Auth::user()->id]]);
			$data = $data->distinct('users.id');
			$data = $data->orderBy('users.id', 'desc')->orderBy('users.updated_at', 'desc');
			$data = $data->get()->toArray();
		}
		else{
		 $data = $data
			->forPage($page, $perPage)
			->withTrashed();			 
			$data = $data->orderBy($orderby, $ordertype)
			->orderBy('updated_at', 'desc')
			->orderBy('created_at', 'desc')
			->orderBy('id', 'desc')
			->get()
			->toArray();
		}
		$lists = $this->modifyList($data);
		
		$count = count($lists);
		return ['count' => $count, 'data' => $lists];
	}
	
	/**
	 * This Method use for modify single list by user id
	*/
	private function modifyList($lists)
	{
		if($lists)
		{
			foreach($lists as $key => $list)
			{
				$lists[$key]['id'] 			=  $list["id"];
				$lists[$key]['first_name'] 	=  $list["first_name"];
				$lists[$key]['last_name'] 	=  $list["last_name"];
				$lists[$key]['email'] 		=  $list["email"];
				$lists[$key]['status'] 		=  $list["status"];
				$lists[$key]['created_at'] 	=  $list["created_at"];
				$lists[$key]['updated_at'] 	=  $list["updated_at"];
				$lists[$key]['is_email'] 	=  $list["is_email"];
				$lists[$key]['tempkey'] 	=  $list["tempkey"];
				$lists[$key]['deleted_at'] 	=  $list["deleted_at"];
				$lists[$key]['default_ledger_id'] 	=  $list['default_ledger_id'];
				
				if(Auth::user()->id == 1) 
				{
					$mapping = UserRoleMapping::select('users_roles_mapping.ledger_id','users_roles_mapping.user_id','users_roles_mapping.user_role_id','users_roles.role_name','ledgers.name')
					->where(["user_id" => $list["id"],"ledger_id" => $list["default_ledger_id"]])
					->leftjoin('users_roles', 'users_roles.id', '=', 'users_roles_mapping.user_role_id')
					->leftjoin('ledgers', 'ledgers.id', '=', 'users_roles_mapping.ledger_id')
					->get();
					
					foreach($mapping as $rolmap ) {				
						$lists[$key]['default_ledger_name'] 	  = $rolmap['name'];
						$lists[$key]['user_role']['user_role_id'] = $rolmap['user_role_id'];
						$lists[$key]['user_role']['role_name']    = $rolmap['role_name'];
					}
				}
				else
				{
					$lists[$key]['default_ledger_name'] 	  = $list['name'];
					$lists[$key]['user_role']['user_role_id'] = $list['user_role_id'];
					$lists[$key]['user_role']['role_name']    = $list['role_name'];
				}
			}
		}
		return $lists;		
	}
	
	/**
	 * find record with given id
	 */
	public function find($id)
	{
		$users =  $this->user
		->select('users.*','users_roles_mapping.id as mapping_id','users_roles_mapping.ledger_id','users_roles_mapping.user_id','users_roles_mapping.user_role_id','users_roles.id as role_id','users_roles.role_name','ledgers.name as ledger_name')
		->join('users_roles_mapping', 'users_roles_mapping.user_id', '=', 'users.id')
		->join('users_roles', 'users_roles.id', '=', 'users_roles_mapping.user_role_id')
		->join('ledgers', 'ledgers.id', '=', 'users_roles_mapping.ledger_id')
		->where('users.id',$id)->get()->toArray();

		return  $this->modifySingleUser($users);
	}

	/**
	 * This Method use for modify single list by user id
	*/
	private function modifySingleUser($users)
	{
		$res = [];
		if($users)
		{
			foreach($users as $key => $user)
			{
				$roleId = $user['user_role_id'];
				$ledgerId = $user['ledger_id'];
				$res['id'] 			=  $user["id"];
				$res['first_name'] 	=  $user["first_name"];
				$res['last_name'] 	=  $user["last_name"];
				$res['email'] 		=  $user["email"];
				$res['status'] 		=  $user["status"];
				$res['created_at'] 	=  $user["created_at"];
				$res['updated_at'] 	=  $user["updated_at"];
				$res['is_email'] 	=  $user["is_email"];
				$res['tempkey'] 	=  $user["tempkey"];
				$res['deleted_at'] 	=  $user["deleted_at"];
				$res['default_ledger_id'] 	=  $user['default_ledger_id'];
				$res['ledgers_role'][$key]['role_id']     = $user['role_id'];
				$res['ledgers_role'][$key]['role_name']   = $user['role_name'];
				$res['ledgers_role'][$key]['ledger_id']   = $user['ledger_id'];
				$res['ledgers_role'][$key]['ledger_name'] = $user['ledger_name'];
			}
		}
		return $res;		
	}

	/**
	 * update record with given id
	 */
	public function update($id, $attributes)
	{
		$attributes['updated_by'] = Auth::user()->id;
		$attributes['updated_at'] = date("Y-m-d H:i:s");
		return $this->user->find($id)->update($attributes);
	}
	/**
	 * delete record with given id
	 */
	public function delete($id, array $attributes)
	{
		$attributes['updated_by'] = Auth::user()->id;
		$attributes['updated_at'] = date("Y-m-d H:i:s");
	    return $this->user->find($id)->delete();
	}
}
