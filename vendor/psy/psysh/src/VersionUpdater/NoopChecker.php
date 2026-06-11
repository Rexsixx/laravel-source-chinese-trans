<?php
/**
 * Psy，版本更新，等待检查
 */

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2018 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\VersionUpdater;

use Psy\Shell;

/**
 * A version checker stub which always thinks the current verion is up to date.
 * 一个版本检查器存根，它总是认为当前版本是最新的。
 */
class NoopChecker implements Checker
{
    /**
     * @return bool
     */
    public function isLatest()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getLatest()
    {
        return Shell::VERSION;
    }
}
