<?php
/**
 * Lcobucci，JWT，令牌，Plain
 */

namespace Lcobucci\JWT\Token;

use Lcobucci\JWT\Token;
use function class_alias;

class_exists(Plain::class, false) || class_alias(Token::class, Plain::class);
