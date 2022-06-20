<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emp_positions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'emp_position_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'emp_position_id',
        'emp_position',
        'emp_code',
    ];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public static function getPositions()
    {
        $positions = PositionModel::all()->toArray();
        return array_values($positions);
    }
}
