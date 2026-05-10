<?php
/**
 * Swift，Swift 地址编码器
 */

/*
 * This file is part of SwiftMailer.
 * (c) 2018 Christian Schmidt
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Email address encoder.
 * 电子邮件地址编码器
 *
 * @author Christian Schmidt
 */
interface Swift_AddressEncoder
{
    /**
     * Encodes an email address.
	 * 编码电子邮件地址
     *
     * @throws Swift_AddressEncoderException if the email cannot be represented in
     *                                       the encoding implemented by this class
     */
    public function encodeString(string $address): string;
}
