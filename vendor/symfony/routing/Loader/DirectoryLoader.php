<?php
/**
 * Symfony，组件，路由选择，加载程序，目录加载器
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Routing\RouteCollection;

class DirectoryLoader extends FileLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $collection = new RouteCollection();
        $collection->addResource(new DirectoryResource($path));

        foreach (scandir($path) as $dir) {
            if ('.' !== $dir[0]) {
                $this->setCurrentDir($path);
                $subPath = $path.'/'.$dir;
                $subType = null;

                if (is_dir($subPath)) {
                    $subPath .= '/';
                    $subType = 'directory';
                }

                $subCollection = $this->import($subPath, $subType, false, $path);
                $collection->addCollection($subCollection);
            }
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        // only when type is forced to directory, not to conflict with AnnotationLoader
		// 只有当类型被强制到目录时,不要与注释加载器冲突。

        return 'directory' === $type;
    }
}
