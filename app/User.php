<?php
/**
 * App，用户
 */

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
	 * 可大量分配的属性
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
	 * 应该为数组隐藏的属性
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
