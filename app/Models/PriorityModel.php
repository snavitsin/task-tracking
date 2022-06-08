<?php

namespace App\Models;

use Faker\Extension\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Extensions\Helpers;

class PriorityModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_priority';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'priority_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'priority_id',
        'priority_title',
        'priority_slug',
    ];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public function getPriority()
    {
        $priority = PriorityModel::all()->toArray();
        return array_values($priority);
    }
}
