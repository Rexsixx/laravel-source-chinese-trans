<?php
/**
 * Nette，定位，翻译器
 */

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Localization;


/**
 * Translator adapter.
 * 翻译适配器。
 */
interface Translator
{
	/**
	 * Translates the given string.
	 * 翻译给定的字符串
	 * @param  mixed  $message
	 * @param  mixed  ...$parameters
	 */
	function translate($message, ...$parameters): string;
}


interface_exists(ITranslator::class);
