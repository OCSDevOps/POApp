<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JMC_MASTER_COST_CODE extends Model
{
    use HasFactory;
    protected $connection='sqlsrv';
    protected $table='JCM_MASTER__COST_CODE';
}
