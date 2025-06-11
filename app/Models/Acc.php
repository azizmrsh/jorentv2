<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Acc extends Model
{
    use HasFactory, HasRoles;

    /**
     * The guard name used by spatie/laravel-permission.
     */
    protected string $guard_name = 'web';
    //
    protected $fillable = [
        'firstname',
        'midname',
        'lastname',
        'email',
        'phone',
        'address',
        'birth_date',
        'profile_photo',
        'password',
        'status', 
        'document_type',
        'document_number', 
        'document_photo', 
        'nationality',
        'hired_date',
        'hired_by',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'birth_date' => 'date',
        'hired_date' => 'date',
    ];
    protected $attributes = [
        'status' => 'active', // active, inactive, banned
        'phone_verified_at' => null,
        'email_verified_at' => null,
    ];
    protected $table = 'accs';  
    
    // relationship with table properties one to many //osaidhaj03

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
    
}
