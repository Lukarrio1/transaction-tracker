<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

trait TracksUserLogin
{
    /**
     * Boot the trait and add model event listeners.
     */
    public function TracksUserLogin()
    {

        $this->updateLastLogin();
    }

    /**
     * Update the last login timestamp.
     *
     * @return void
     */
    protected function updateLastLogin()
    {
        // $this->setLastLoginAtAttribute(Carbon::now());
        // $this->saveQuietly(); // Avoid recursion by using saveQuietly
    }

    /**
     * Scope a query to only include users inactive for a specified number of months.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $months Number of months to consider as inactive
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactiveForMonths(Builder $query, int $months = 1)
    {
        return $query->where('last_login_at', '<', Carbon::now()->subMonths($months));
    }
}
