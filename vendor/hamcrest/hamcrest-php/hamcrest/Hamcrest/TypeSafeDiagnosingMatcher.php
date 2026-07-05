<?php
/**
 * Hamcrest，类型安全诊断Matcher
 */

namespace Hamcrest;

/**
 * Convenient base class for Matchers that require a value of a specific type.
 * This simply checks the type and then casts.
 */

abstract class TypeSafeDiagnosingMatcher extends TypeSafeMatcher
{

    final public function matchesSafely($item)
    {
        return $this->matchesSafelyWithDiagnosticDescription($item, new NullDescription());
    }

    final public function describeMismatchSafely($item, Description $mismatchDescription)
    {
        $this->matchesSafelyWithDiagnosticDescription($item, $mismatchDescription);
    }

    // -- Protected Methods

    /**
     * Subclasses should implement these. The item will already have been checked for
     * the specific type.
	 * 子类应该实现这些。这个项目已经被检查了具体类型。
     */
    abstract protected function matchesSafelyWithDiagnosticDescription($item, Description $mismatchDescription);
}
