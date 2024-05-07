<?php namespace RainLab\User\Classes;

use Closure;
use Detection\MobileDetect;

/**
 * Agent
 *
 * @package rainlab\user
 * @author Jens Segers
 * @copyright https://github.com/jenssegers/agent
 */
class Agent extends MobileDetect
{
    /**
     * @var array additionalOperatingSystems list
     */
    protected static $additionalOperatingSystems = [
        'Windows' => 'Windows',
        'Windows NT' => 'Windows NT',
        'OS X' => 'Mac OS X',
        'Debian' => 'Debian',
        'Ubuntu' => 'Ubuntu',
        'Macintosh' => 'PPC',
        'OpenBSD' => 'OpenBSD',
        'Linux' => 'Linux',
        'ChromeOS' => 'CrOS',
    ];

    /**
     * @var array additionalBrowsers list
     */
    protected static $additionalBrowsers = [
        'Opera Mini' => 'Opera Mini',
        'Opera' => 'Opera|OPR',
        'Edge' => 'Edge|Edg',
        'Coc Coc' => 'coc_coc_browser',
        'UCBrowser' => 'UCBrowser',
        'Vivaldi' => 'Vivaldi',
        'Chrome' => 'Chrome',
        'Firefox' => 'Firefox',
        'Safari' => 'Safari',
        'IE' => 'MSIE|IEMobile|MSIEMobile|Trident/[.0-9]+',
        'Netscape' => 'Netscape',
        'Mozilla' => 'Mozilla',
        'WeChat' => 'MicroMessenger',
    ];

    /**
     * @var array store for for resolved strings
     */
    protected $store = [];

    /**
     * @return string|null platform name from the User Agent.
     */
    public function platform()
    {
        return $this->retrieveUsingCacheOrResolve('jetstream.platform', function () {
            return $this->findDetectionRulesAgainstUserAgent(
                $this->mergeRules(MobileDetect::getOperatingSystems(), static::$additionalOperatingSystems)
            );
        });
    }

    /**
     * @return string|null browser name from the User Agent
     */
    public function browser()
    {
        return $this->retrieveUsingCacheOrResolve('jetstream.browser', function () {
            return $this->findDetectionRulesAgainstUserAgent(
                $this->mergeRules(static::$additionalBrowsers, MobileDetect::getBrowsers())
            );
        });
    }

    /**
     * @return bool isDesktop determine if the device is a desktop computer.
     */
    public function isDesktop()
    {
        return $this->retrieveUsingCacheOrResolve('jetstream.desktop', function () {
            // Check specifically for cloudfront headers if the useragent === 'Amazon CloudFront'
            if (
                $this->getUserAgent() === static::$cloudFrontUA &&
                $this->getHttpHeader('HTTP_CLOUDFRONT_IS_DESKTOP_VIEWER') === 'true'
            ) {
                return true;
            }

            return !$this->isMobile() && !$this->isTablet();
        });
    }

    /**
     * findDetectionRulesAgainstUserAgent matches a detection rule
     * and return the matched key.
     */
    protected function findDetectionRulesAgainstUserAgent(array $rules)
    {
        $userAgent = $this->getUserAgent();

        foreach ($rules as $key => $regex) {
            if (empty($regex)) {
                continue;
            }

            if ($this->match($regex, $userAgent)) {
                return $key ?: reset($this->matchesArray);
            }
        }

        return null;
    }

    /**
     * retrieveUsingCacheOrResolve retrieves from the given key from the cache
     * or resolve the value.
     */
    protected function retrieveUsingCacheOrResolve(string $key, Closure $callback)
    {
        $cacheKey = $this->createCacheKey($key);

        if (!is_null($cacheItem = $this->store[$cacheKey] ?? null)) {
            return $cacheItem;
        }

        return $this->store[$cacheKey] = call_user_func($callback);
    }

    /**
     * mergeRules merges multiple rules into one array.
     */
    protected function mergeRules(...$all)
    {
        $merged = [];

        foreach ($all as $rules) {
            foreach ($rules as $key => $value) {
                if (empty($merged[$key])) {
                    $merged[$key] = $value;
                }
                elseif (is_array($merged[$key])) {
                    $merged[$key][] = $value;
                }
                else {
                    $merged[$key] .= '|'.$value;
                }
            }
        }

        return $merged;
    }
}
