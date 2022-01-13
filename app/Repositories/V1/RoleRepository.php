<?php

namespace App\Repositories\V1;

use App\Repositories\BaseRepository;
use App\Models\Role;
use Auth;

class RoleRepository extends BaseRepository
{
	/**
	 * @var role
	 */
	private $role;

	/**
	 * Role constructor.
	 * @param ReportData $Role
	 */
	public function __construct(Role $role)
	{
		$this->role = $role;
		$this->isSuperAdmin    = Auth::check() ? Auth::user()->isSupperAdmin() : 0;
	}

	public function create($attributes)
	{
		$attributes['created_at'] = date("Y-m-d H:i:s");
        $attributes['updated_at'] = date("Y-m-d H:i:s");
        $attributes['created_by'] = Auth::user()->id;
        $attributes['updated_by'] = Auth::user()->id;
		return $this->role->create($attributes);
	}
	/**
	 * Listing with custom search with id, role_name,updated_at
	 */
	public function list(int $userId, int $page, int $perPage, string $filter = null, $request): array
	{
		$model = $this->role;
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
                if ($key == "role_name") {
						
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
			$data = $data
				->WhereRaw($whr);
		}
		$data = $data
			->forPage($page, $perPage)
			->with('users:id,first_name,last_name','getRoleMapping:user_role_id,ledger_id,user_id')
			->orderBy($orderby, $ordertype)
			->orderBy('updated_at', 'desc')
			->orderBy('created_at', 'desc')
			->orderBy('id', 'desc')
			->get()
			->toArray();
		$lists = $this->modifyList($data);
		$count = count($lists);
		return ['count' => $count, 'data' => $lists];
	}

	/**
	 * Modify fy list for get no of user by role 
	*/
	private function modifyList($lists)
	{
		if($lists) {
			foreach($lists as $key => $list) {
				$getRoleMappings = $list['get_role_mapping'];
				$users = [];
				foreach($getRoleMappings as $ky => $getRoleMapping) {
					$users[$key][$getRoleMapping['user_id']] = $getRoleMapping;
				}
				$lists[$key]['no_of_users'] = !empty($users[$key]) ? count($users[$key]) : 0;
			}
		}
		return $lists;
	}
	
	/**
	 * find record with given id
	 */
	public function find($id)
	{
		return $this->role->find($id);
	}
	/**
	 * update record with given id
	 */
	public function update($id, array $attributes)
	{
		$attributes['updated_at'] = date("Y-m-d H:i:s");
		$attributes['updated_by'] = Auth::user()->id;
		return $this->role->find($id)->update($attributes);
	}
	/**
	 * delete record with given id
	 */
	public function delete($id)
	{
	    return $this->role->find($id)->delete();
	}
}
