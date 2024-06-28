<?php namespace RainLab\User\Classes;

use App;
use Illuminate\Routing\Redirector;

/**
 * UserRedirector
 *
 * @todo this is a black box; nearly impossible to find from the `Redirect` facade,
 * the `october\rain` library should  provide an interface to change the key name,
 * that is, if Laravel doesn't add this common feature in a later version.
 */
class UserRedirector extends Redirector
{
    /**
     * intended creates a new redirect response to the previously intended location.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function intended($default = '/', $status = 302, $headers = [], $secure = null)
    {
        if (!App::runningInFrontend()) {
            return parent::intended($default, $status, $headers, $secure);
        }

        $path = $this->session->pull('url.cms.intended', $default);

        return $this->to($path, $status, $headers, $secure);
    }

    /**
     * getIntendedUrl from the session.
     */
    public function getIntendedUrl()
    {
        if (!App::runningInFrontend()) {
            return parent::getIntendedUrl();
        }

        return $this->session->get('url.cms.intended');
    }

    /**
     * setIntendedUrl in the session.
     */
    public function setIntendedUrl($url)
    {
        if (!App::runningInFrontend()) {
            return parent::setIntendedUrl($url);
        }

        $this->session->put('url.cms.intended', $url);
        return $this;
    }
}
