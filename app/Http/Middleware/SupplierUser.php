<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Notifications\SupplierResetPasswordNotification;
use App\Notifications\SupplierVerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SupplierUser extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'company_id', 'supplier_id', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'sup_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new SupplierResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new SupplierVerifyEmailNotification);
    }
}