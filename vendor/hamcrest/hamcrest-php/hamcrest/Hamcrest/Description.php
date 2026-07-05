<?php
/**
 * Hamcrest，描述
 */

namespace Hamcrest;

/*
 Copyright (c) 2009 hamcrest.org
 */

/**
 * A description of a Matcher. A Matcher will describe itself to a description
 * which can later be used for reporting.
 * 对Matcher的描述。Matcher将描述自己描述的描述,稍后可以用于报告。
 *
 * @see Hamcrest\Matcher::describeTo()
 */
interface Description
{

    /**
     * Appends some plain text to the description.
	 * 将一些普通文本添加到描述中
     *
     * @param string $text
     *
     * @return \Hamcrest\Description
     */
    public function appendText($text);

    /**
     * Appends the description of a {@link Hamcrest\SelfDescribing} value to
     * this description.
     *
     * @param \Hamcrest\SelfDescribing $value
     *
     * @return \Hamcrest\Description
     */
    public function appendDescriptionOf(SelfDescribing $value);

    /**
     * Appends an arbitary value to the description.
	 * 将仲裁值附加到描述
     *
     * @param mixed $value
     *
     * @return \Hamcrest\Description
     */
    public function appendValue($value);

    /**
     * Appends a list of values to the description.
     *
     * @param string $start
     * @param string $separator
     * @param string $end
     * @param array|\IteratorAggregate|\Iterator $values
     *
     * @return \Hamcrest\Description
     */
    public function appendValueList($start, $separator, $end, $values);

    /**
     * Appends a list of {@link Hamcrest\SelfDescribing} objects to the
     * description.
     *
     * @param string $start
     * @param string $separator
     * @param string $end
     * @param array|\\IteratorAggregate|\\Iterator $values
     *   must be instances of {@link Hamcrest\SelfDescribing}
     *
     * @return \Hamcrest\Description
     */
    public function appendList($start, $separator, $end, $values);
}
