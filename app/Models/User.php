<?php

namespace App\Models;

use App\Database\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * @{inheritdoc}
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'display', 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @{inheritdoc}
     */
    protected $searchable = [
        'username', 'display'
    ];

    /**
     * @{inheritdoc}
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Set default value for the password field
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        if (! empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }
}
