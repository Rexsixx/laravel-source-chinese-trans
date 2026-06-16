<?php
/**
 * Symfony，组件，Css 选择器，XPath，扩展，扩展接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\XPath\Extension;

/**
 * XPath expression translator extension interface.
 * XPath表达式转换器扩展接口。
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
interface ExtensionInterface
{
    /**
     * Returns node translators.
	 * 返回节点翻译器。
     *
     * These callables will receive the node as first argument and the translator as second argument.
     *
     * @return callable[]
     */
    public function getNodeTranslators(): array;

    /**
     * Returns combination translators.
     *
     * @return callable[]
     */
    public function getCombinationTranslators(): array;

    /**
     * Returns function translators.
     *
     * @return callable[]
     */
    public function getFunctionTranslators(): array;

    /**
     * Returns pseudo-class translators.
     *
     * @return callable[]
     */
    public function getPseudoClassTranslators(): array;

    /**
     * Returns attribute operation translators.
     *
     * @return callable[]
     */
    public function getAttributeMatchingTranslators(): array;

    /**
     * Returns extension name.
     */
    public function getName(): string;
}
