<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JMC_MASTER_JOB extends Model
{
    use HasFactory;
    protected $connection='sqlsrv';
    protected $table='JCM_MASTER__JOB';
}
