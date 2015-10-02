<?php namespace RainLab\User\Models;

use October\Rain\Auth\Models\Group as GroupBase;

/**
 * User Group Model
 */
class UserGroup extends GroupBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'user_groups';

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required|between:3,64',
        'code' => 'required|regex:/^[a-zA-Z0-9_\-]+$/|unique:user_groups',
    ];

    /**
     * @var array Relations
     */
    public $belongsToMany = [
        'users'       => ['RainLab\User\Models\User', 'table' => 'users_groups'],
        'users_count' => ['RainLab\User\Models\User', 'table' => 'users_groups', 'count' => true]
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'description'
    ];
}