<?php
/**
 * Illuminate，视图，编译器，Blade 编译器
 */

namespace Illuminate\View\Compilers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BladeCompiler extends Compiler implements CompilerInterface
{
    use Concerns\CompilesAuthorizations,
        Concerns\CompilesComments,
        Concerns\CompilesComponents,
        Concerns\CompilesConditionals,
        Concerns\CompilesEchos,
        Concerns\CompilesHelpers,
        Concerns\CompilesIncludes,
        Concerns\CompilesInjections,
        Concerns\CompilesJson,
        Concerns\CompilesLayouts,
        Concerns\CompilesLoops,
        Concerns\CompilesRawPhp,
        Concerns\CompilesStacks,
        Concerns\CompilesTranslations;

    /**
     * All of the registered extensions.
	 * 所有已注册的扩展
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * All custom "directive" handlers.
	 * 所有自定义“指令”处理程序
     *
     * @var array
     */
    protected $customDirectives = [];

    /**
     * All custom "condition" handlers.
	 * 所有自定义“条件”处理程序
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * The file currently being compiled.
	 * 目前正在编译的文件
     *
     * @var string
     */
    protected $path;

    /**
     * All of the available compiler functions.
	 * 所有可用的编译器函数
     *
     * @var array
     */
    protected $compilers = [
        'Comments',
        'Extensions',
        'Statements',
        'Echos',
    ];

    /**
     * Array of opening and closing tags for raw echos.
	 * 对原始回声的打开和关闭标记的数组
     *
     * @var array
     */
    protected $rawTags = ['{!!', '!!}'];

    /**
     * Array of opening and closing tags for regular echos.
	 * 定期回音的打开和关闭标签的数组
     *
     * @var array
     */
    protected $contentTags = ['{{', '}}'];

    /**
     * Array of opening and closing tags for escaped echos.
	 * 开封和关闭标签的数组
     *
     * @var array
     */
    protected $escapedTags = ['{{{', '}}}'];

    /**
     * The "regular" / legacy echo string format.
	 * “常规”/遗留回波字符串格式
     *
     * @var string
     */
    protected $echoFormat = 'e(%s)';

    /**
     * Array of footer lines to be added to template.
	 * 将页脚线数组添加到模板中
     *
     * @var array
     */
    protected $footer = [];

    /**
     * Array to temporary store the raw blocks found in the template.
	 * 数组到临时存储模板中发现的原始块
     *
     * @var array
     */
    protected $rawBlocks = [];

    /**
     * Compile the view at the given path.
	 * 在给定路径上编译视图
     *
     * @param  string  $path
     * @return void
     */
    public function compile($path = null)
    {
        if ($path) {
            $this->setPath($path);
        }

        if (! is_null($this->cachePath)) {
            $contents = $this->compileString($this->files->get($this->getPath()));

            $this->files->put($this->getCompiledPath($this->getPath()), $contents);
        }
    }

    /**
     * Get the path currently being compiled.
	 * 获取当前正在编译的路径
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path currently being compiled.
	 * 设置当前正在编译的路径
     *
     * @param  string  $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Compile the given Blade template contents.
	 * 编译给定的Blade模板内容
     *
     * @param  string  $value
     * @return string
     */
    public function compileString($value)
    {
        if (strpos($value, '@verbatim') !== false) {
            $value = $this->storeVerbatimBlocks($value);
        }

        $this->footer = [];

        if (strpos($value, '@php') !== false) {
            $value = $this->storePhpBlocks($value);
        }

        $result = '';

        // Here we will loop through all of the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (token_get_all($value) as $token) {
            $result .= is_array($token) ? $this->parseToken($token) : $token;
        }

        if (! empty($this->rawBlocks)) {
            $result = $this->restoreRawContent($result);
        }

        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.
        if (count($this->footer) > 0) {
            $result = $this->addFooters($result);
        }

        return $result;
    }

    /**
     * Store the verbatim blocks and replace them with a temporary placeholder.
	 * 存储逐字块并用临时占位符替换它们
     *
     * @param  string  $value
     * @return string
     */
    protected function storeVerbatimBlocks($value)
    {
        return preg_replace_callback('/(?<!@)@verbatim(.*?)@endverbatim/s', function ($matches) {
            return $this->storeRawBlock($matches[1]);
        }, $value);
    }

    /**
     * Store the PHP blocks and replace them with a temporary placeholder.
	 * 存储PHP块并用临时占位符替换它们
     *
     * @param  string  $value
     * @return string
     */
    protected function storePhpBlocks($value)
    {
        return preg_replace_callback('/(?<!@)@php(.*?)@endphp/s', function ($matches) {
            return $this->storeRawBlock("<?php{$matches[1]}?>");
        }, $value);
    }

    /**
     * Store a raw block and return a unique raw placeholder.
	 * 存储一个原始块并返回一个唯一的原始占位符
     *
     * @param  string  $value
     * @return string
     */
    protected function storeRawBlock($value)
    {
        return $this->getRawPlaceholder(
            array_push($this->rawBlocks, $value) - 1
        );
    }

    /**
     * Replace the raw placeholders with the original code stored in the raw blocks.
	 * 用存储在原始块中的原始代码替换原始占位符
     *
     * @param  string  $result
     * @return string
     */
    protected function restoreRawContent($result)
    {
        $result = preg_replace_callback('/'.$this->getRawPlaceholder('(\d+)').'/', function ($matches) {
            return $this->rawBlocks[$matches[1]];
        }, $result);

        $this->rawBlocks = [];

        return $result;
    }

    /**
     * Get a placeholder to temporary mark the position of raw blocks.
	 * 获取一个占位符来临时标记原始块的位置
     *
     * @param  int|string  $replace
     * @return string
     */
    protected function getRawPlaceholder($replace)
    {
        return str_replace('#', $replace, '@__raw_block_#__@');
    }

    /**
     * Add the stored footers onto the given content.
	 * 将存储的页脚添加到给定的内容中
     *
     * @param  string  $result
     * @return string
     */
    protected function addFooters($result)
    {
        return ltrim($result, PHP_EOL)
                .PHP_EOL.implode(PHP_EOL, array_reverse($this->footer));
    }

    /**
     * Parse the tokens from the template.
	 * 从模板解析标记
     *
     * @param  array  $token
     * @return string
     */
    protected function parseToken($token)
    {
        [$id, $content] = $token;

        if ($id == T_INLINE_HTML) {
            foreach ($this->compilers as $type) {
                $content = $this->{"compile{$type}"}($content);
            }
        }

        return $content;
    }

    /**
     * Execute the user defined extensions.
	 * 执行用户定义的扩展
     *
     * @param  string  $value
     * @return string
     */
    protected function compileExtensions($value)
    {
        foreach ($this->extensions as $compiler) {
            $value = call_user_func($compiler, $value, $this);
        }

        return $value;
    }

    /**
     * Compile Blade statements that start with "@".
	 * 用“@”开始编译刀片语句
     *
     * @param  string  $value
     * @return string
     */
    protected function compileStatements($value)
    {
        return preg_replace_callback(
            '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', function ($match) {
                return $this->compileStatement($match);
            }, $value
        );
    }

    /**
     * Compile a single Blade @ statement.
	 * 编译单个刀片@语句
     *
     * @param  array  $match
     * @return string
     */
    protected function compileStatement($match)
    {
        if (Str::contains($match[1], '@')) {
            $match[0] = isset($match[3]) ? $match[1].$match[3] : $match[1];
        } elseif (isset($this->customDirectives[$match[1]])) {
            $match[0] = $this->callCustomDirective($match[1], Arr::get($match, 3));
        } elseif (method_exists($this, $method = 'compile'.ucfirst($match[1]))) {
            $match[0] = $this->$method(Arr::get($match, 3));
        }

        return isset($match[3]) ? $match[0] : $match[0].$match[2];
    }

    /**
     * Call the given directive with the given value.
	 * 用给定的值调用给定的指令
     *
     * @param  string  $name
     * @param  string|null  $value
     * @return string
     */
    protected function callCustomDirective($name, $value)
    {
        if (Str::startsWith($value, '(') && Str::endsWith($value, ')')) {
            $value = Str::substr($value, 1, -1);
        }

        return call_user_func($this->customDirectives[$name], trim($value));
    }

    /**
     * Strip the parentheses from the given expression.
	 * 从给定的表达式中删除括号
     *
     * @param  string  $expression
     * @return string
     */
    public function stripParentheses($expression)
    {
        if (Str::startsWith($expression, '(')) {
            $expression = substr($expression, 1, -1);
        }

        return $expression;
    }

    /**
     * Register a custom Blade compiler.
	 * 注册一个自定义的Blade编译器
     *
     * @param  callable  $compiler
     * @return void
     */
    public function extend(callable $compiler)
    {
        $this->extensions[] = $compiler;
    }

    /**
     * Get the extensions used by the compiler.
	 * 获取编译器使用的扩展
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Register an "if" statement directive.
	 * 注册一个“if”语句指令
     *
     * @param  string  $name
     * @param  callable  $callback
     * @return void
     */
    public function if($name, callable $callback)
    {
        $this->conditions[$name] = $callback;

        $this->directive($name, function ($expression) use ($name) {
            return $expression !== ''
                    ? "<?php if (\Illuminate\Support\Facades\Blade::check('{$name}', {$expression})): ?>"
                    : "<?php if (\Illuminate\Support\Facades\Blade::check('{$name}')): ?>";
        });

        $this->directive('else'.$name, function ($expression) use ($name) {
            return $expression !== ''
                ? "<?php elseif (\Illuminate\Support\Facades\Blade::check('{$name}', {$expression})): ?>"
                : "<?php elseif (\Illuminate\Support\Facades\Blade::check('{$name}')): ?>";
        });

        $this->directive('end'.$name, function () {
            return '<?php endif; ?>';
        });
    }

    /**
     * Check the result of a condition.
	 * 检查条件的结果
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return bool
     */
    public function check($name, ...$parameters)
    {
        return call_user_func($this->conditions[$name], ...$parameters);
    }

    /**
     * Register a component alias directive.
	 * 注册一个组件别名指令
     *
     * @param  string  $path
     * @param  string  $alias
     * @return void
     */
    public function component($path, $alias = null)
    {
        $alias = $alias ?: array_last(explode('.', $path));

        $this->directive($alias, function ($expression) use ($path) {
            return $expression
                        ? "<?php \$__env->startComponent('{$path}', {$expression}); ?>"
                        : "<?php \$__env->startComponent('{$path}'); ?>";
        });

        $this->directive('end'.$alias, function ($expression) {
            return '<?php echo $__env->renderComponent(); ?>';
        });
    }

    /**
     * Register an include alias directive.
	 * 注册一个包含别名指令
     *
     * @param  string  $path
     * @param  string  $alias
     * @return void
     */
    public function include($path, $alias = null)
    {
        $alias = $alias ?: array_last(explode('.', $path));

        $this->directive($alias, function ($expression) use ($path) {
            $expression = $this->stripParentheses($expression) ?: '[]';

            return "<?php echo \$__env->make('{$path}', {$expression}, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";
        });
    }

    /**
     * Register a handler for custom directives.
	 * 为自定义指令注册一个处理程序
     *
     * @param  string  $name
     * @param  callable  $handler
     * @return void
     */
    public function directive($name, callable $handler)
    {
        $this->customDirectives[$name] = $handler;
    }

    /**
     * Get the list of custom directives.
	 * 获取自定义指令列表
     *
     * @return array
     */
    public function getCustomDirectives()
    {
        return $this->customDirectives;
    }

    /**
     * Set the echo format to be used by the compiler.
	 * 设置编译器要使用的echo格式
     *
     * @param  string  $format
     * @return void
     */
    public function setEchoFormat($format)
    {
        $this->echoFormat = $format;
    }

    /**
     * Set the "echo" format to double encode entities.
	 * 将“echo”格式设置为对实体进行双编码
     *
     * @return void
     */
    public function withDoubleEncoding()
    {
        $this->setEchoFormat('e(%s, true)');
    }

    /**
     * Set the "echo" format to not double encode entities.
	 * 将“echo”格式设置为不对实体进行双重编码
     *
     * @return void
     */
    public function withoutDoubleEncoding()
    {
        $this->setEchoFormat('e(%s, false)');
    }
}
