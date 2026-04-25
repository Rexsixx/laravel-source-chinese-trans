<?php
/**
 * Illuminate，Cookie，Cookie 值前缀
 */

namespace Illuminate\Cookie;

use Illuminate\Support\Str;

class CookieValuePrefix
{
    /**
     * Create a new cookie value prefix for the given cookie name.
	 * 为给定的cookie名称创建一个新的cookie值前缀
     *
     * @param  string  $cookieName
     * @param  string  $key
     * @return string
     */
    public static function create($cookieName, $key)
    {
        return hash_hmac('sha1', $cookieName.'v2', $key).'|';
    }

    /**
     * Remove the cookie value prefix.
	 * 删除cookie值前缀
     *
     * @param  string  $cookieValue
     * @return string
     */
    public static function remove($cookieValue)
    {
        return substr($cookieValue, 41);
    }

    /**
     * Verify the provided cookie's value.
	 * 验证提供的cookie的值
     *
     * @param  string  $name
     * @param  string  $value
     * @param  string  $key
     * @return string|null
     */
    public static function getVerifiedValue($name, $value, $key)
    {
        $verifiedValue = null;

        if (Str::startsWith($value, static::create($name, $key))) {
            $verifiedValue = static::remove($value);
        }

        return $verifiedValue;
    }
}
