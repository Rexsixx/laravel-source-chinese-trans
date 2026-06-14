<?php
/**
 * Symfony，组件，控制台，描述符，描述接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Descriptor;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Descriptor interface.
 * 描述接口
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
interface DescriptorInterface
{
    /**
     * Describes an object if supported.
	 * 如果支持,描述一个对象。
     *
     * @param object $object
     */
    public function describe(OutputInterface $output, $object, array $options = []);
}
