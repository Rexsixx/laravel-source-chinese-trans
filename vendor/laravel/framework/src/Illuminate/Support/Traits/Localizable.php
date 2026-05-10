<?php
/**
 * Illuminate，支持，特性，可定位的
 */

namespace Illuminate\Support\Traits;

trait Localizable
{
    /**
     * Run the callback with the given locale.
	 * 使用给定的区域运行回调
     *
     * @param  string  $locale
     * @param  \Illuminate\Contracts\Translation\Translator  $translator
     * @param  \Closure  $callback
     * @return bool
     */
    public function withLocale($locale, $translator, $callback)
    {
        if (! $locale || ! $translator) {
            return $callback();
        }

        $original = $translator->getLocale();

        try {
            $translator->setLocale($locale);

            return $callback();
        } finally {
            $translator->setLocale($original);
        }
    }
}
