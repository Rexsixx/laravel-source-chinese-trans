<?php
/**
 * Hamcrest，文本，是同样忽略的情况
 */

namespace Hamcrest\Text;

/*
 Copyright (c) 2009 hamcrest.org
 */
use Hamcrest\Description;
use Hamcrest\TypeSafeMatcher;

/**
 * Tests if a string is equal to another string, regardless of the case.
 * 测试如果字符串等于另一个字符串,不管情况如何。
 */
class IsEqualIgnoringCase extends TypeSafeMatcher
{

    private $_string;

    public function __construct($string)
    {
        parent::__construct(self::TYPE_STRING);

        $this->_string = $string;
    }

    protected function matchesSafely($item)
    {
        return strtolower($this->_string) === strtolower($item);
    }

    protected function describeMismatchSafely($item, Description $mismatchDescription)
    {
        $mismatchDescription->appendText('was ')->appendText($item);
    }

    public function describeTo(Description $description)
    {
        $description->appendText('equalToIgnoringCase(')
                                ->appendValue($this->_string)
                                ->appendText(')')
                                ;
    }

    /**
     * Matches if value is a string equal to $string, regardless of the case.
	 * 匹配如果值是一个字符串等于$ string,不管情况如何。
     *
     * @factory
     */
    public static function equalToIgnoringCase($string)
    {
        return new self($string);
    }
}
