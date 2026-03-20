<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ContractDocument extends Model
{
    use CompanyScope;

    protected $table = 'contract_documents';
    protected $primaryKey = 'cdoc_id';
    public $timestamps = false;

    const TYPE_COI = 'coi';
    const TYPE_SIGNED_CONTRACT = 'signed_contract';
    const TYPE_W9 = 'w9';
    const TYPE_LIEN_WAIVER = 'lien_waiver';
    const TYPE_INSURANCE_CERT = 'insurance_cert';
    const TYPE_LICENSE = 'license';
    const TYPE_OTHER = 'other';

    protected $fillable = [
        'cdoc_contract_id',
        'cdoc_original_name',
        'cdoc_path',
        'cdoc_mime',
        'cdoc_size',
        'cdoc_type',
        'cdoc_description',
        'cdoc_createby',
        'cdoc_createdate',
        'cdoc_status',
        'company_id',
    ];

    protected $casts = [
        'cdoc_createdate' => 'datetime',
        'cdoc_size' => 'integer',
        'cdoc_status' => 'integer',
    ];

    // ── Relationships ──

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'cdoc_contract_id', 'contract_id');
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('cdoc_status', 1);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('cdoc_type', $type);
    }

    // ── Helpers ──

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_COI => 'Certificate of Insurance',
            self::TYPE_SIGNED_CONTRACT => 'Signed Contract',
            self::TYPE_W9 => 'W-9 Tax Form',
            self::TYPE_LIEN_WAIVER => 'Lien Waiver',
            self::TYPE_INSURANCE_CERT => 'Insurance Certificate',
            self::TYPE_LICENSE => 'License/Certification',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public function getTypeTextAttribute()
    {
        return self::getTypeOptions()[$this->cdoc_type] ?? 'Other';
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->cdoc_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
