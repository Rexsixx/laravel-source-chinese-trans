<?php
/**
 * League，Flysystem，适配器，Polyfill，流读特性
 */

namespace League\Flysystem\Adapter\Polyfill;

/**
 * A helper for adapters that only handle strings to provide read streams.
 * 适配器的一个助手,它只处理字符串来提供读取流。
 */
trait StreamedReadingTrait
{
    /**
     * Reads a file as a stream.
	 * 将文件读取为流
     *
     * @param string $path
     *
     * @return array|false
     *
     * @see League\Flysystem\ReadInterface::readStream()
     */
    public function readStream($path)
    {
        if ( ! $data = $this->read($path)) {
            return false;
        }

        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $data['contents']);
        rewind($stream);
        $data['stream'] = $stream;
        unset($data['contents']);

        return $data;
    }

    /**
     * Reads a file.
	 * 读取文件
     *
     * @param string $path
     *
     * @return array|false
     *
     * @see League\Flysystem\ReadInterface::read()
     */
    abstract public function read($path);
}
