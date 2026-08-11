<?php
/**
 * Egulias，Flare Client，契约，提供者 Flare上下文
 */

namespace Facade\FlareClient\Contracts;

interface ProvidesFlareContext
{
    public function context(): array;
}
