<?php
/**
 * League，Flysystem，适配器，可以覆盖文件
 */

namespace League\Flysystem\Adapter;

/**
 * Adapters that implement this interface let the Filesystem know that files can be overwritten using the write
 * functions and don't need the update function to be called. This can help improve performance when asserts are disabled.
 * 实现这个接口的适配器让文件系统知道,文件可以使用写入函数重写,不需要调用更新函数。当断言被禁用时,这可以帮助提高性能。
 */
interface CanOverwriteFiles
{
}
