<?php
/**
 * Symfony，组件，Http内核，分析器，分析器存储接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Profiler;

/**
 * ProfilerStorageInterface.
 * 分析器存储接口。
 *
 * This interface exists for historical reasons. The only supported
 * implementation is FileProfilerStorage.
 *
 * As the profiler must only be used on non-production servers, the file storage
 * is more than enough and no other implementations will ever be supported.
 *
 * @internal since 4.2
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface ProfilerStorageInterface
{
    /**
     * Finds profiler tokens for the given criteria.
	 * 查找给定条件的分析器令牌
     *
     * @param string   $ip     The IP
     * @param string   $url    The URL
     * @param string   $limit  The maximum number of tokens to return
     * @param string   $method The request method
     * @param int|null $start  The start date to search from
     * @param int|null $end    The end date to search to
     *
     * @return array An array of tokens
     */
    public function find($ip, $url, $limit, $method, $start = null, $end = null): array;

    /**
     * Reads data associated with the given token.
	 * 读取与给定标记相关联的数据。
     *
     * The method returns false if the token does not exist in the storage.
     *
     * @param string $token A token
     *
     * @return Profile|null The profile associated with token
     */
    public function read($token): ?Profile;

    /**
     * Saves a Profile.
	 * 保存配置文件
     *
     * @return bool Write operation successful
     */
    public function write(Profile $profile): bool;

    /**
     * Purges all data from the database.
	 * 从数据库中清除所有数据
     */
    public function purge();
}
