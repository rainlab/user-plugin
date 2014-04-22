<?php namespace RainLab\User\Models;

use Model;

/**
 * State Model
 */
class State extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'rainlab_user_states';

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
    public $belongsTo = [
        'country' => ['RainLab\User\Models\Country']
    ];

    /**
     * @var bool Indicates if the model should be timestamped.
     */
    public $timestamps = false;

}