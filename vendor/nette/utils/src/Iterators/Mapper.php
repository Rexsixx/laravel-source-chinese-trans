<?php
/**
 * Nette，迭代器，映射
 */

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Iterators;



/**
 * Applies the callback to the elements of the inner iterator.
 * 将回调函数应用于内部迭代器的元素。
 */
class Mapper extends \IteratorIterator
{
	/** @var callable */
	private $callback;


	public function __construct(\Traversable $iterator, callable $callback)
	{
		parent::__construct($iterator);
		$this->callback = $callback;
	}


	#[\ReturnTypeWillChange]
	public function current()
	{
		return ($this->callback)(parent::current(), parent::key());
	}
}
