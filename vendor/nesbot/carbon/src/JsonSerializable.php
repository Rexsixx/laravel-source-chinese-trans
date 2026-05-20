<?php
/**
 * Carbon，可序列化的
 */

interface JsonSerializable
{
    /**
     * Specify data which should be serialized to JSON.
	 * 指定应该序列化到JSON的数据
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize();
}
