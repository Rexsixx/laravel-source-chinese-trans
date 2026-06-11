<?php
/**
 * Illuminate，支持，门面，Lang
 */

namespace Illuminate\Support\Facades;

/**
 * @method static mixed trans(string $key, array $replace = [], string $locale = null)
 * @method static string transChoice(string $key, int|array|\Countable $number, array $replace = [], string $locale = null)
 * @method static string getLocale()
 * @method static void setLocale(string $locale)
 * @method static string|array|null get(string $key, array $replace = [], string $locale = null, bool $fallback = true)
 *
 * @see \Illuminate\Translation\Translator
 */
class Lang extends Facade
{
    /**
     * Get the registered name of the component.
	 * 获取组件的注册名称
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'translator';
    }
}
