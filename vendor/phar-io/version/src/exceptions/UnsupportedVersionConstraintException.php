<?php
/**
 * PharIo，版本，不受支持的版本约束异常
 */

/*
 * This file is part of PharIo\Version.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PharIo\Version;

final class UnsupportedVersionConstraintException extends \RuntimeException implements Exception {
}
