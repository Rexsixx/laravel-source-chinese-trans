<?php
/**
 * Lcobucci，JWT，令牌，注册索赔
 */

namespace Lcobucci\JWT\Token;

/**
 * Defines the list of claims that are registered in the IANA "JSON Web Token Claims" registry
 *
 * @see https://tools.ietf.org/html/rfc7519#section-4.1
 */
interface RegisteredClaims
{
    const ALL = [
        self::AUDIENCE,
        self::EXPIRATION_TIME,
        self::ID,
        self::ISSUED_AT,
        self::ISSUER,
        self::NOT_BEFORE,
        self::SUBJECT,
    ];

    const DATE_CLAIMS = [
        self::ISSUED_AT,
        self::NOT_BEFORE,
        self::EXPIRATION_TIME,
    ];

    /**
     * Identifies the recipients that the JWT is intended for
	 * 确定JWT的目的
     *
     * @see https://tools.ietf.org/html/rfc7519#section-4.1.3
     */
    const AUDIENCE = 'aud';

    /**
     * Identifies the expiration time on or after which the JWT MUST NOT be accepted for processing
	 * 确定在或之后,JWT不能接受处理
     *
     * @see https://tools.ietf.org/html/rfc7519#section-4.1.4
     */
    const EXPIRATION_TIME = 'exp';

    /**
     * Provides a unique identifier for the JWT
	 * 为JWT提供一个唯一的标识符
     *
     * @see https://tools.ietf.org/html/rfc7519#section-4.1.7
     */
    const ID = 'jti';

    /**
     * Identifies the time at which the JWT was issued
	 * 确定JWT发布的时间
     *
     * @see https://tools.ietf.org/html/rfc7519#section-4.1.6
     */
    const ISSUED_AT = 'iat';

    /**
     * Identifies the principal that issued the JWT
     *
     * @see https://tools.ietf.org/html/rfc7519#section-4.1.1
     */
    const ISSUER = 'iss';

    /**
     * Identifies the time before which the JWT MUST NOT be accepted for processing
     *
     * https://tools.ietf.org/html/rfc7519#section-4.1.5
     */
    const NOT_BEFORE = 'nbf';

    /**
     * Identifies the principal that is the subject of the JWT.
     *
     * https://tools.ietf.org/html/rfc7519#section-4.1.2
     */
    const SUBJECT = 'sub';
}
