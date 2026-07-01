<?php
/**
 * Symfony，组件，翻译，载入程序，Ini File 加载器
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Loader;

/**
 * IniFileLoader loads translations from an ini file.
 * IniFileLoader加载来自ini文件的翻译。
 *
 * @author stealth35
 */
class IniFileLoader extends FileLoader
{
    /**
     * {@inheritdoc}
     */
    protected function loadResource($resource)
    {
        return parse_ini_file($resource, true);
    }
}
