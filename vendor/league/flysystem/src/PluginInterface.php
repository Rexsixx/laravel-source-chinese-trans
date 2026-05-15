<?php
/**
 * League，Flysystem，插件接口
 */

namespace League\Flysystem;

interface PluginInterface
{
    /**
     * Get the method name.
	 * 获取方法名
     *
     * @return string
     */
    public function getMethod();

    /**
     * Set the Filesystem object.
	 * 设置Filesystem对象
     *
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem);
}
