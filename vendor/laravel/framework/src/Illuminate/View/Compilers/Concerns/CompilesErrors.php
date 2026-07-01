<?php
/**
 * Illuminate，视图，编译，问题，编译错误
 */

namespace Illuminate\View\Compilers\Concerns;

trait CompilesErrors
{
    /**
     * Compile the error statements into valid PHP.
	 * 将错误语句编译成有效的PHP
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileError($expression)
    {
        $expression = $this->stripParentheses($expression);

        return '<?php if ($errors->has('.$expression.')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('.$expression.'); ?>';
    }

    /**
     * Compile the enderror statements into valid PHP.
	 * 将这些错误语句编译成有效的PHP
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileEnderror($expression)
    {
        return '<?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>';
    }
}
