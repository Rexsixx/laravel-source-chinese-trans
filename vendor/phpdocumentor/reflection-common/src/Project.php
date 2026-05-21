<?php
/**
 * phpDocumentor，Reflection，项目
 */

declare(strict_types=1);

/**
 * phpDocumentor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

/**
 * Interface for project. Since the definition of a project can be different per factory this interface will be small.
 * 项目接口。由于项目的定义可以是不同的工厂,这个接口将很小。
 */
interface Project
{
    /**
     * Returns the name of the project.
	 * 返回项目的名称
     */
    public function getName() : string;
}
