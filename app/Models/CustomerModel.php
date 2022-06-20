<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'customer_id';

    protected $appends = ['customer_projects'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'customer_fio',
        'customer_email',
        'customer_phone',
    ];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public function getCustomers()
    {
        $customers = CustomerModel::all()->toArray();
        return array_values($customers);
    }

    public function getCustomer()
    {
        $customerId = $this->attributes['customer_id'];
        return CustomerModel::find($customerId)->toArray();
    }

    public function updateCustomer()
    {
        $isNewCustomer = !isset($this->attributes['customer_id']);
        $result = null;
        if($isNewCustomer) {
            $customer = new CustomerModel($this->attributes);
            $result = $customer->save();
        }
        else {
            $customer = CustomerModel::find($this->attributes['customer_id']);
            $result = null;

            if ($customer) {
                $customer->attributes = $this->attributes;
                $result = $customer->save();
            }
        }

        return boolval($result);
    }

    public function deleteCustomer() {
        $customerId = $this->attributes['customer_id'];
        $customer = CustomerModel::find($customerId);
        return $customer->delete();
    }

    public function getCustomerProjectsAttribute()
    {
        return ProjectModel::where('project_customer', $this->attributes['customer_id'])->get()->all();
    }
}
