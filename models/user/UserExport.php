<?php namespace RainLab\User\Models\User;

use Backend\Models\ExportModel;
use RainLab\User\Models\User;

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
     * @var array protectedColumns are never included in the export, even when
     * present in the column configuration, to avoid leaking sensitive values.
     */
    protected $protectedColumns = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * exportData
     */
    public function exportData($columns, $sessionKey = null)
    {
        $records = User::with(['groups', 'primary_group'])->get();

        $result = [];
        foreach ($records as $record) {
            $item = [];
            foreach ($columns as $column) {
                $item[$column] = $this->encodeUserAttribute($record, $column);
            }
            $result[] = $item;
        }

        return $result;
    }

    /**
     * encodeUserAttribute
     */
    protected function encodeUserAttribute($record, $column)
    {
        if (in_array($column, $this->protectedColumns)) {
            return '';
        }

        if ($column === 'groups') {
            return $this->encodeGroupsValue($record);
        }

        if ($column === 'primary_group') {
            return $record->primary_group->code ?? '';
        }

        return $record->{$column};
    }

    /**
     * encodeGroupsValue returns the user groups as a pipe-separated list of codes.
     */
    protected function encodeGroupsValue($record)
    {
        if (!$record->groups || $record->groups->isEmpty()) {
            return '';
        }

        return $this->encodeArrayValue(
            $record->groups->pluck('code')->all()
        );
    }
}
