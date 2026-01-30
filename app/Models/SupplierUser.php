<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SupplierUser extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'supplier_users';
    protected $primaryKey = 'id';

    /**
     * Use default timestamps (created_at/updated_at).
     */
    public $timestamps = true;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'supplier_id',
        'company_id',
        'status',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'integer',
        'company_id' => 'integer',
        'supplier_id' => 'integer',
    ];

    /**
     * Apply company scope automatically.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    /**
     * Relationship to supplier master record.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'sup_id');
    }

    /**
     * Relationship to owning company.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Send password reset notification with supplier-specific route.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\SupplierResetPasswordNotification($token));
    }

    /**
     * Send email verification notification with supplier-specific route.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\SupplierVerifyEmailNotification);
    }
}
