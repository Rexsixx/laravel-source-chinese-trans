<?php
/**
 * Lcobucci，JWT，Claim
 */

/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT;

use JsonSerializable;

/**
 * Basic interface for token claims
 * 象征性声明的基本接口
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.0.0
 */
interface Claim extends JsonSerializable
{
    /**
     * Returns the claim name
	 * 返回索赔名称
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the claim value
	 * 返回索赔值
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Returns the string representation of the claim
	 * 返回索赔的字符串表示
     *
     * @return string
     */
    public function __toString();
}
