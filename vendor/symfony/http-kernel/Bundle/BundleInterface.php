<?php
/**
 * Symfony，组件，HttpKernel，Bundle，Bundle 接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Bundle;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * BundleInterface.
 * 包的接口
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface BundleInterface extends ContainerAwareInterface
{
    /**
     * Boots the Bundle.
	 * 启动Bundle
     */
    public function boot();

    /**
     * Shutdowns the Bundle.
	 * 关闭Bundle
     */
    public function shutdown();

    /**
     * Builds the bundle.
	 * 构建包。
     *
     * It is only ever called once when the cache is empty.
	 * 只有在缓存是空的时候才调用它。
     */
    public function build(ContainerBuilder $container);

    /**
     * Returns the container extension that should be implicitly loaded.
	 * 返回应该隐式加载的容器扩展
     *
     * @return ExtensionInterface|null The default extension or null if there is none
     */
    public function getContainerExtension();

    /**
     * Returns the bundle name (the class short name).
	 * 返回包名（类的短名称）
     *
     * @return string The Bundle name
     */
    public function getName();

    /**
     * Gets the Bundle namespace.
     *
     * @return string The Bundle namespace
     */
    public function getNamespace();

    /**
     * Gets the Bundle directory path.
     *
     * The path should always be returned as a Unix path (with /).
     *
     * @return string The Bundle absolute path
     */
    public function getPath();
}
