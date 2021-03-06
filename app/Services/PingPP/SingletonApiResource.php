<?php

namespace App\Services\PingPP;

abstract class SingletonApiResource extends ApiResource
{
    protected static function _singletonRetrieve($options = null)
    {
        $opts = Util\RequestOptions::parse($options);
        $opts->mergeSignOpts(static::$signOpts);
        $instance = new static(null, $opts);
        $instance->refresh();
        return $instance;
    }

    /**
     * @param SingletonApiResource $class
     * @return string The endpoint associated with this singleton class.
     */
    public static function classUrl()
    {
        $base = static::className();
        return "/v1/${base}";
    }

    /**
     * @return string The endpoint associated with this singleton API resource.
     */
    public function instanceUrl()
    {
        return static::classUrl();
    }
}
