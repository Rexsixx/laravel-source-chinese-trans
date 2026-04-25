<?php
/**
 * Illuminate，验证，规则，唯一
 */

namespace Illuminate\Validation\Rules;

class Unique
{
    use DatabaseRule;

    /**
     * The ID that should be ignored.
	 * 应该被忽略的ID
     *
     * @var mixed
     */
    protected $ignore;

    /**
     * The name of the ID column.
	 * ID列的名称
     *
     * @var string
     */
    protected $idColumn = 'id';

    /**
     * Ignore the given ID during the unique check.
	 * 在唯一性检查期间忽略给定的ID
     *
     * @param  mixed  $id
     * @param  string  $idColumn
     * @return $this
     */
    public function ignore($id, $idColumn = 'id')
    {
        $this->ignore = $id;
        $this->idColumn = $idColumn;

        return $this;
    }

    /**
     * Convert the rule to a validation string.
	 * 将规则转换为验证字符串
     *
     * @return string
     */
    public function __toString()
    {
        return rtrim(sprintf('unique:%s,%s,%s,%s,%s',
            $this->table,
            $this->column,
            $this->ignore ? '"'.$this->ignore.'"' : 'NULL',
            $this->idColumn,
            $this->formatWheres()
        ), ',');
    }
}
