<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionTemplate extends Model
{
    use HasFactory;

    protected $table = 'permission_master';
    protected $primaryKey = 'pt_id';
    public $timestamps = false;

    protected $fillable = [
        'pt_template_name',
        'pt_template_users',
        'pt_t_porder',
        'pt_t_rorder',
        'pt_t_rcorder',
        'pt_t_rfq',
        'pt_m_item',
        'pt_m_uom',
        'pt_m_costcode',
        'pt_m_projects',
        'pt_m_suppliers',
        'pt_m_taxgroup',
        'pt_m_budget',
        'pt_m_email',
        'pt_i_item',
        'pt_i_itemp',
        'pt_i_supplierc',
        'pt_e_eq',
        'pt_e_eqm',
        'pt_e_checklist',
        'pt_a_user',
        'pt_a_permissions',
        'pt_a_cinfo',
        'pt_a_procore',
        'created_date',
        'status',
    ];
}
