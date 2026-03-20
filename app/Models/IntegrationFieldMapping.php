<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationFieldMapping extends Model
{
    use HasFactory;

    protected $table = 'integration_field_mappings';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'integration_id',
        'company_id',
        'entity_type',
        'local_field',
        'external_field',
        'transformation',
        'transformation_params',
        'is_active',
    ];

    protected $casts = [
        'transformation_params' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the integration that owns this mapping.
     */
    public function integration()
    {
        return $this->belongsTo(AccountingIntegration::class, 'integration_id', 'id');
    }

    /**
     * Apply transformation to a value.
     */
    public function transform($value)
    {
        if (!$this->transformation) {
            return $value;
        }

        return match($this->transformation) {
            'uppercase' => strtoupper($value),
            'lowercase' => strtolower($value),
            'date_format' => $this->transformDate($value),
            'currency' => number_format((float)$value, 2, '.', ''),
            'boolean_to_string' => $value ? 'true' : 'false',
            'string_to_boolean' => in_array(strtolower($value), ['1', 'true', 'yes']),
            default => $value,
        };
    }

    /**
     * Transform date value based on params.
     */
    private function transformDate($value): string
    {
        if (!$value) {
            return '';
        }

        $format = $this->transformation_params['format'] ?? 'Y-m-d';
        
        if ($value instanceof \DateTime) {
            return $value->format($format);
        }

        return date($format, strtotime($value));
    }
}
