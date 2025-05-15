<?php

namespace Workdo\SalesAgent\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\SalesAgent\Entities\SalesAgent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use \Workdo\ProductService\Entities\ProductService;

class Program extends Model
{
    use HasFactory;

    protected $table = 'sales_agents_programs';

    protected $fillable = [
        'id',
        'name',
        'from_date',
        'to_date',
        'description',
        'discount_type',
        'sales_agents_applicable',
        'sales_agents_view',
        'program_details',
        'workspace',
        'created_by',
    ];


    // User
    public function userApplicable()
    {
        return $this->belongsTo(User::class, 'sales_agents_applicable', 'id');
    }

    public function items()
    {
        return $this->hasMany(ProgramItems::class);
    }

    public function productServices()
    {
        return $this->hasManyThrough(ProductService::class, ProgramItems::class, 'program_id', 'id', 'id', 'item');
    }


    public function getSalesAgent()
    {
        if (!empty($this->sales_agents_applicable)) {
            $members_ids = explode(',', $this->sales_agents_applicable);
            $members = User::select('id', 'name', 'avatar')->whereIn('id', $members_ids)->get();
            return $members;
        } else {
            return [];
        }
    }

    public static function getProgramsBySalesAgentId($user_id = '')
    {
        return DB::table('sales_agents_programs')->where('workspace', getActiveWorkSpace())->whereRaw('FIND_IN_SET(?, sales_agents_applicable)', [$user_id ?? \Auth::user()->id])->pluck('name', 'id');
    }

    public function getSalesAgentAll()
    {
        if (!empty($this->sales_agents_applicable)) {
            $members_ids = explode(',', $this->sales_agents_applicable);

            $salesAgents = User::where('workspace_id', getActiveWorkSpace())
                ->leftjoin('customers', 'users.id', '=', 'customers.user_id')
                ->leftjoin('sales_agents', 'users.id', '=', 'sales_agents.user_id')
                ->where('users.type', 'salesagent')
                ->where('users.is_disable', '1')
                ->whereIn('users.id', $members_ids)
                ->get();

            return $salesAgents;
        } else {
            return [];
        }
    }



    public static function getProductServices(array $ids = [])
    {
        $productServices = ProductService::where('workspace_id', getActiveWorkSpace());

        if (!empty($ids)) {
            $program_details = ProgramItems::whereIn('program_id', $ids)->get();

            $items = [];
            foreach ($program_details as $key => $program_detail) {
                $items[$key] = $program_detail->items;
            }

            $flattenedArray = [];
            foreach ($items as $subArray) {
                $flattenedArray = array_merge($flattenedArray, explode(',', $subArray));
            }

            $productServices->whereIn('id', $flattenedArray);
        }

        return $productServices->get();
    }

    public static function getProductServicesItems($id = '')
    {
        if ($id !== '') {
            $program_details = ProgramItems::where('program_id', $id)->get();
        } else {
            $program_details = ProgramItems::get();
        }
        foreach ($program_details as $key => $program_detail) {
            $items[$key]         = $program_detail->items;
        }
        $flattenedArray = [];
        foreach ($items as $subArray) {
            $flattenedArray = array_merge($flattenedArray, explode(',', $subArray));
        }

        return $flattenedArray;
    }

    public static function getUsersData()
    {
        $sales_agents_programs = \DB::table('sales_agents_programs')->get();

        $employeeIds = [];

        foreach ($sales_agents_programs as $item) {
            $employees = explode(',', $item->sales_agents_applicable);
            foreach ($employees as $employee) {
                $employeeIds[] = $employee;
            }
        }

        $data = [];
        $users =  User::whereIn('id', array_unique($employeeIds))->get();

        foreach ($users as $user) {
            $data[$user->id]['name']        = $user->name;
            $data[$user->id]['avatar']      = $user->avatar;
        }

        return $data;
    }
    public static function getProgramItems($id = '')
    {
        $items = [];

        if ($id !== '') {
            $program_details = ProgramItems::where('program_id', $id)->get();
        } else {
            $program_details = ProgramItems::get();
        }

        foreach ($program_details as $program_detail) {
            $items[$program_detail->id] = explode(',', $program_detail->items);
          
        }
       
      
        return $items;
    }
}
