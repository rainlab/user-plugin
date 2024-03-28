<?php namespace RainLab\User\Components\Account;

use Db;
use Auth;
use Config;
use Request;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use ValidationException;

/**
 * ActionBrowserSessions
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionBrowserSessions
{
    /**
     * fetchSessions
     */
    protected function fetchSessions()
    {
        if (Config::get('session.driver') !== 'database') {
            return [];
        }

        return collect(
            Db::connection(Config::get('session.connection'))
                ->table(Config::get('session.table', 'sessions'))
                ->where('user_id', Auth::user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) {
            $agent = $this->createAgent($session);

            return (object) [
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser()
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === Request::session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans()
            ];
        })->all();
    }

    /**
     * actionDeleteOtherSessions
     */
    protected function actionDeleteOtherSessions()
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        $password = (string) post('password');

        if (!$this->isUserPasswordValid($password)) {
            throw new ValidationException([
                'password' => __('This password does not match our records.'),
            ]);
        }

        Auth::logoutOtherDevices($password);

        Db::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->getAuthIdentifier())
            ->where('id', '!=', Request::session()->getId())
            ->delete();
    }

    /**
     * createAgent instance from the given session.
     */
    protected function createAgent($session): Agent
    {
        $agent = new Agent;
        $agent->setUserAgent($session->user_agent);
        return $agent;
    }
}
