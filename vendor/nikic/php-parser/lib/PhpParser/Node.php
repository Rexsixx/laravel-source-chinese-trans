<?php declare(strict_types=1);

/**
 * PhpParser，节点
 */

namespace PhpParser;

interface Node
{
    /**
     * Gets the type of the node.
	 * 获取节点的类型
     *
     * @return string Type of the node
     */
    public function getType() : string;

    /**
     * Gets the names of the sub nodes.
	 * 获取子节点的名称
     *
     * @return array Names of sub nodes
     */
    public function getSubNodeNames() : array;

    /**
     * Gets line the node started in (alias of getStartLine).
	 * 获取节点开始于(getStartLine别名)的行
     *
     * @return int Start line (or -1 if not available)
     */
    public function getLine() : int;

    /**
     * Gets line the node started in.
	 * 获取节点开始的行。
     *
     * Requires the 'startLine' attribute to be enabled in the lexer (enabled by default).
     *
     * @return int Start line (or -1 if not available)
     */
    public function getStartLine() : int;

    /**
     * Gets the line the node ended in.
	 * 获取节点结束的行。
     *
     * Requires the 'endLine' attribute to be enabled in the lexer (enabled by default).
     *
     * @return int End line (or -1 if not available)
     */
    public function getEndLine() : int;

    /**
     * Gets the token offset of the first token that is part of this node.
	 * 获取第一个标记的令牌偏移,这是该节点的一部分。
     *
     * The offset is an index into the array returned by Lexer::getTokens().
     *
     * Requires the 'startTokenPos' attribute to be enabled in the lexer (DISABLED by default).
     *
     * @return int Token start position (or -1 if not available)
     */
    public function getStartTokenPos() : int;

    /**
     * Gets the token offset of the last token that is part of this node.
	 * 获取最后一个标记的令牌偏移,这是该节点的一部分。
     *
     * The offset is an index into the array returned by Lexer::getTokens().
     *
     * Requires the 'endTokenPos' attribute to be enabled in the lexer (DISABLED by default).
     *
     * @return int Token end position (or -1 if not available)
     */
    public function getEndTokenPos() : int;

    /**
     * Gets the file offset of the first character that is part of this node.
	 * 获取第一个字符的文件偏移量,它是这个节点的一部分。
     *
     * Requires the 'startFilePos' attribute to be enabled in the lexer (DISABLED by default).
     *
     * @return int File start position (or -1 if not available)
     */
    public function getStartFilePos() : int;

    /**
     * Gets the file offset of the last character that is part of this node.
	 * 获取该节点的最后一个字符的文件偏移量。
     *
     * Requires the 'endFilePos' attribute to be enabled in the lexer (DISABLED by default).
     *
     * @return int File end position (or -1 if not available)
     */
    public function getEndFilePos() : int;

    /**
     * Gets all comments directly preceding this node.
	 * 所有的注释直接在这个节点前面。
     *
     * The comments are also available through the "comments" attribute.
     *
     * @return Comment[]
     */
    public function getComments() : array;

    /**
     * Gets the doc comment of the node.
	 * 获取节点的doc注释
     *
     * @return null|Comment\Doc Doc comment object or null
     */
    public function getDocComment();

    /**
     * Sets the doc comment of the node.
	 * 设置节点的doc注释。
     *
     * This will either replace an existing doc comment or add it to the comments array.
     *
     * @param Comment\Doc $docComment Doc comment to set
     */
    public function setDocComment(Comment\Doc $docComment);

    /**
     * Sets an attribute on a node.
	 * 在节点上设置属性
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setAttribute(string $key, $value);

    /**
     * Returns whether an attribute exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute(string $key) : bool;

    /**
     * Returns the value of an attribute.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute(string $key, $default = null);

    /**
     * Returns all the attributes of this node.
     *
     * @return array
     */
    public function getAttributes() : array;

    /**
     * Replaces all the attributes of this node.
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes);
}
