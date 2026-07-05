<?php
/**
 * Hamcrest，核心，所有
 */

namespace Hamcrest\Core;

/*
 Copyright (c) 2009 hamcrest.org
 */
use Hamcrest\Description;
use Hamcrest\Util;

/**
 * Calculates the logical disjunction of multiple matchers. Evaluation is
 * shortcut, so subsequent matchers are not called if an earlier matcher
 * returns <code>true</code>.
 * 计算多个摄场的逻辑分解。
 */
class AnyOf extends ShortcutCombination
{

    public function __construct(array $matchers)
    {
        parent::__construct($matchers);
    }

    public function matches($item)
    {
        return $this->matchesWithShortcut($item, true);
    }

    public function describeTo(Description $description)
    {
        $this->describeToWithOperator($description, 'or');
    }

    /**
     * Evaluates to true if ANY of the passed in matchers evaluate to true.
	 * 如果在matchers中通过的任何一个被通过的人都评估为真
     *
     * @factory ...
     */
    public static function anyOf(/* args... */)
    {
        $args = func_get_args();

        return new self(Util::createMatcherArray($args));
    }

    /**
     * Evaluates to false if ANY of the passed in matchers evaluate to true.
     *
     * @factory ...
     */
    public static function noneOf(/* args... */)
    {
        $args = func_get_args();

        return IsNot::not(
            new self(Util::createMatcherArray($args))
        );
    }
}
