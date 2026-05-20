<?php
/**
 * Doctrine，Inflector，循环单词影响器
 */

declare(strict_types=1);

namespace Doctrine\Inflector;

class NoopWordInflector implements WordInflector
{
    public function inflect(string $word): string
    {
        return $word;
    }
}
