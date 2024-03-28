<?php namespace RainLab\User\Models\User;

use Backend\Models\ExportModel;
use ApplicationException;

/**
 * UserExport Model
 */
class UserExport extends ExportModel
{
    /**
     * @var string table used by the model
     */
    protected $table = 'users';

    /**
     * exportData
     */
    public function exportData($columns, $sessionKey = null)
    {
        return [];
    }
}
