<?php namespace RainLab\User\Models\User;

/**
 * HasModelScopes
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasModelScopes
{
    /**
     * scopeApplyRegistered
     */
    public function scopeApplyRegistered($query)
    {
        return $query->where('is_guest', false);
    }

    /**
     * applyStatusCode
     */
    public function scopeApplyStatusCode($query, $value)
    {
        if ($value instanceof \Backend\Classes\FilterScope) {
            $value = $value->value;
        }

        if ($value === 'deleted') {
            return $query->onlyTrashed();
        }

        if ($value === 'active') {
            return $query->withoutTrashed()->whereNotNull('activated_at');
        }

        if ($value === 'inactive') {
            return $query->withoutTrashed()->whereNull('activated_at');
        }

        return $query->withoutTrashed();
    }

    /**
     * scopeIsActivated
     */
    public function scopeIsActivated($query)
    {
        return $query->whereNotNull('activated_at');
    }

    /**
     * scopeApplyPrimaryGroup
     */
    public function scopeApplyPrimaryGroup($query, $filter)
    {
        if ($filter instanceof \Backend\Classes\FilterScope) {
            $filter = $filter->value;
        }

        return $query->whereIn('primary_group_id', (array) $filter);
    }

    /**
     * scopeApplyGroups
     */
    public function scopeApplyGroups($query, $filter)
    {
        return $query->whereHas('groups', function($group) use ($filter) {
            $group->whereIn('id', $filter);
        });
    }

    /**
     * @deprecated
     */
    public function scopeFilterByGroup($query, $filter)
    {
        return $this->scopeApplyGroups($query, $filter);
    }
}
