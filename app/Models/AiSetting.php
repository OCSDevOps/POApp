<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    use CompanyScope;

    protected $table = 'ai_settings';
    protected $primaryKey = 'ai_setting_id';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'ai_provider',
        'api_key',
        'model_name',
        'max_tokens',
        'temperature',
        'is_active',
        'ai_createby',
        'ai_createdate',
        'ai_modifyby',
        'ai_modifydate',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'temperature' => 'decimal:2',
        'max_tokens' => 'integer',
    ];

    protected $hidden = ['api_key'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function getDecryptedApiKeyAttribute(): ?string
    {
        if (empty($this->api_key)) {
            return null;
        }
        try {
            return decrypt($this->api_key);
        } catch (\Exception $e) {
            return $this->api_key;
        }
    }

    public function getMaskedApiKeyAttribute(): string
    {
        $key = $this->decrypted_api_key;
        if (empty($key)) {
            return '';
        }
        return substr($key, 0, 7) . str_repeat('*', max(0, strlen($key) - 11)) . substr($key, -4);
    }
}
