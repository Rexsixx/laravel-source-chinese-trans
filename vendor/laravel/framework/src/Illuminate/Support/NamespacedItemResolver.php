<?php
/**
 * Illuminate，支持，命名空间项解析器
 */

namespace Illuminate\Support;

class NamespacedItemResolver
{
    /**
     * A cache of the parsed items.
	 * 已解析项的缓存
     *
     * @var array
     */
    protected $parsed = [];

    /**
     * Parse a key into namespace, group, and item.
	 * 将键解析为名称空间、组和项。
     *
     * @param  string  $key
     * @return array
     */
    public function parseKey($key)
    {
        // If we've already parsed the given key, we'll return the cached version we
        // already have, as this will save us some processing. We cache off every
        // key we parse so we can quickly return it on all subsequent requests.
		// 如果已经解析了给定的键，我们将返回我们之前缓存的版本，因为这样可以节省一些处理时间。
		// 对于每次解析出的键，我们都会进行缓存，以便在后续的所有请求中都能快速返回该键。
        if (isset($this->parsed[$key])) {
            return $this->parsed[$key];
        }

        // If the key does not contain a double colon, it means the key is not in a
        // namespace, and is just a regular configuration item. Namespaces are a
        // tool for organizing configuration items for things such as modules.
		// 如果键中不包含双冒号，这意味着该键并非位于命名空间中，而只是一个普通的配置项。
		// 命名空间是用于对诸如模块之类的配置项进行组织的工具。
        if (strpos($key, '::') === false) {
            $segments = explode('.', $key);

            $parsed = $this->parseBasicSegments($segments);
        } else {
            $parsed = $this->parseNamespacedSegments($key);
        }

        // Once we have the parsed array of this key's elements, such as its groups
        // and namespace, we will cache each array inside a simple list that has
        // the key and the parsed array for quick look-ups for later requests.
		// 一旦我们获取了此键的元素的解析数组（例如其组和命名空间），
		// 我们就会将每个数组缓存到一个简单的列表中，该列表包含键和已解析的数组，以便在后续请求中快速查找。
        return $this->parsed[$key] = $parsed;
    }

    /**
     * Parse an array of basic segments.
	 * 解析基本段数组
     *
     * @param  array  $segments
     * @return array
     */
    protected function parseBasicSegments(array $segments)
    {
        // The first segment in a basic array will always be the group, so we can go
        // ahead and grab that segment. If there is only one total segment we are
        // just pulling an entire group out of the array and not a single item.
		// 基本数组中的第一个部分总是代表整个组，所以我们可以直接获取这个部分。
		// 基本数组中的第一个部分总是代表整个组，所以我们可以直接获取这个部分。
		// 如果总共有一个部分，那么我们只需从数组中取出整个组，而不是单个元素。
        $group = $segments[0];

        // If there is more than one segment in this group, it means we are pulling
        // a specific item out of a group and will need to return this item name
        // as well as the group so we know which item to pull from the arrays.
		// 如果这个组中存在多个部分，这意味着我们正在从一个组中取出一个特定的项目，
		// 并且还需要返回该项目的名称以及该组的信息，以便我们能够从数组中准确地取出该项目。
        $item = count($segments) === 1
                    ? null
                    : implode('.', array_slice($segments, 1));

        return [null, $group, $item];
    }

    /**
     * Parse an array of namespaced segments.
	 * 解析一个命名空间段数组
     *
     * @param  string  $key
     * @return array
     */
    protected function parseNamespacedSegments($key)
    {
        [$namespace, $item] = explode('::', $key);

        // First we'll just explode the first segment to get the namespace and group
        // since the item should be in the remaining segments. Once we have these
        // two pieces of data we can proceed with parsing out the item's value.
		// 首先，我们将先解析出第一个段落，以获取命名空间和组信息，因为该项应该存在于其余的段落中。
		// 一旦我们获取了这两组数据，就可以继续解析出该项的值了。
        $itemSegments = explode('.', $item);

        $groupAndItem = array_slice(
            $this->parseBasicSegments($itemSegments), 1
        );

        return array_merge([$namespace], $groupAndItem);
    }

    /**
     * Set the parsed value of a key.
	 * 设置键的解析值
     *
     * @param  string  $key
     * @param  array   $parsed
     * @return void
     */
    public function setParsedKey($key, $parsed)
    {
        $this->parsed[$key] = $parsed;
    }
}
