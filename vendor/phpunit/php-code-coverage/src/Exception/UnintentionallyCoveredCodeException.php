<?php
/**
 * SebastianBergmann，代码覆盖率，无意中覆盖代码异常
 */

/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage;

/**
 * Exception that is raised when code is unintentionally covered.
 * 上升异常当代码没有被故意覆盖时。
 */
final class UnintentionallyCoveredCodeException extends RuntimeException
{
    /**
     * @var array
     */
    private $unintentionallyCoveredUnits = [];

    public function __construct(array $unintentionallyCoveredUnits)
    {
        $this->unintentionallyCoveredUnits = $unintentionallyCoveredUnits;

        parent::__construct($this->toString());
    }

    public function getUnintentionallyCoveredUnits(): array
    {
        return $this->unintentionallyCoveredUnits;
    }

    private function toString(): string
    {
        $message = '';

        foreach ($this->unintentionallyCoveredUnits as $unit) {
            $message .= '- ' . $unit . "\n";
        }

        return $message;
    }
}
