<?php

namespace App\Models;

use Faker\Extension\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Extensions\Helpers;

class MainModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

}
