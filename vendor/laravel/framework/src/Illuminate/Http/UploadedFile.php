<?php
/**
 * Illuminate，Http，已上传文件
 */

namespace Illuminate\Http;

use Illuminate\Support\Arr;
use Illuminate\Container\Container;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class UploadedFile extends SymfonyUploadedFile
{
    use FileHelpers, Macroable;

    /**
     * Begin creating a new file fake.
	 * 开始创建一个新的文件fake
     *
     * @return \Illuminate\Http\Testing\FileFactory
     */
    public static function fake()
    {
        return new Testing\FileFactory;
    }

    /**
     * Store the uploaded file on a filesystem disk.
	 * 将上传的文件存储在文件系统磁盘上
     *
     * @param  string  $path
     * @param  array|string  $options
     * @return string|false
     */
    public function store($path, $options = [])
    {
        return $this->storeAs($path, $this->hashName(), $this->parseOptions($options));
    }

    /**
     * Store the uploaded file on a filesystem disk with public visibility.
	 * 将上传的文件存储在具有公共可见性的文件系统磁盘上
     *
     * @param  string  $path
     * @param  array|string  $options
     * @return string|false
     */
    public function storePublicly($path, $options = [])
    {
        $options = $this->parseOptions($options);

        $options['visibility'] = 'public';

        return $this->storeAs($path, $this->hashName(), $options);
    }

    /**
     * Store the uploaded file on a filesystem disk with public visibility.
	 * 将上传的文件存储在具有公共可见性的文件系统磁盘上
     *
     * @param  string  $path
     * @param  string  $name
     * @param  array|string  $options
     * @return string|false
     */
    public function storePubliclyAs($path, $name, $options = [])
    {
        $options = $this->parseOptions($options);

        $options['visibility'] = 'public';

        return $this->storeAs($path, $name, $options);
    }

    /**
     * Store the uploaded file on a filesystem disk.
	 * 将上传的文件存储在文件系统磁盘上
     *
     * @param  string  $path
     * @param  string  $name
     * @param  array|string  $options
     * @return string|false
     */
    public function storeAs($path, $name, $options = [])
    {
        $options = $this->parseOptions($options);

        $disk = Arr::pull($options, 'disk');

        return Container::getInstance()->make(FilesystemFactory::class)->disk($disk)->putFileAs(
            $path, $this, $name, $options
        );
    }

    /**
     * Create a new file instance from a base instance.
	 * 从基本实例创建新的文件实例
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $file
     * @param  bool $test
     * @return static
     */
    public static function createFromBase(SymfonyUploadedFile $file, $test = false)
    {
        return $file instanceof static ? $file : new static(
            $file->getPathname(),
            $file->getClientOriginalName(),
            $file->getClientMimeType(),
            $file->getClientSize(),
            $file->getError(),
            $test
        );
    }

    /**
     * Parse and format the given options.
	 * 解析并格式化给定的选项
     *
     * @param  array|string  $options
     * @return array
     */
    protected function parseOptions($options)
    {
        if (is_string($options)) {
            $options = ['disk' => $options];
        }

        return $options;
    }
}
