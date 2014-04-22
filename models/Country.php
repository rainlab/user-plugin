<?php namespace RainLab\User\Models;

use Model;

/**
 * Country Model
 */
class Country extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'rainlab_user_countries';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Validation rules
     */
    public $rules = [];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'states' => ['RainLab\User\Models\State']
    ];

    /**
     * @var bool Indicates if the model should be timestamped.
     */
    public $timestamps = false;

}