<?php
/**
 * Lcobucci，JWT，令牌，签名
 */

namespace Lcobucci\JWT\Token;

use Lcobucci\JWT\Signature as SignatureImpl;
use function class_alias;

class_exists(Signature::class, false) || class_alias(SignatureImpl::class, Signature::class);
