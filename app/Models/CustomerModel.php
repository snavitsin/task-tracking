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

    public function getSubdivision()
    {
        $subdivId = $this->attributes['subdiv_id'];
        return SubdivisionModel::find($subdivId)->toArray();
    }

    public function updateSubdiv()
    {
        $subdivId = $this->attributes['subdiv_id'];
        $subdiv = SubdivisionModel::find($subdivId);
        $result = null;
        if ($subdiv) {
            $subdiv->attributes = $this->attributes;
            $result = $subdiv->save();
        }
        return $result;
    }

    public function deleteSubdiv() {
        $subdivId = $this->attributes['subdiv_id'];
        $task = SubdivisionModel::find($subdivId);
        return $task->delete();
    }
}
