<?php
/**
 * Symfony，组件，路由选择，加载程序，对象路由加载器
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

@trigger_error(sprintf('The "%s" class is deprecated since Symfony 4.4, use "%s" instead.', ObjectRouteLoader::class, ObjectLoader::class), \E_USER_DEPRECATED);

/**
 * A route loader that calls a method on an object to load the routes.
 * 在对象上调用方法加载路由的路由加载程序。
 *
 * @author Ryan Weaver <ryan@knpuniversity.com>
 *
 * @deprecated since Symfony 4.4, use ObjectLoader instead.
 */
abstract class ObjectRouteLoader extends ObjectLoader
{
    /**
     * Returns the object that the method will be called on to load routes.
	 * 返回该方法将被调用加载路由的对象。
     *
     * For example, if your application uses a service container,
     * the $id may be a service id.
     *
     * @param string $id
     *
     * @return object
     */
    abstract protected function getServiceObject($id);

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'service' === $type;
    }

    /**
     * {@inheritdoc}
     */
    protected function getObject(string $id)
    {
        return $this->getServiceObject($id);
    }
}
