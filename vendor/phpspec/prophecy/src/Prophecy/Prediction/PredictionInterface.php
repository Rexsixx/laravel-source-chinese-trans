<?php
/**
 * Prophecy，预测，预测接口
 */

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Prediction;

use Prophecy\Call\Call;
use Prophecy\Exception\Prediction\PredictionException;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Prediction interface.
 * Predictions are logical test blocks, tied to `should...` keyword.
 * 预测接口。预测是逻辑测试块,绑定到“应该……”关键词。
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface PredictionInterface
{
    /**
     * Tests that double fulfilled prediction.
	 * 测试双重实现的预测
     *
     * @param Call[]        $calls
     * @param ObjectProphecy<object> $object
     * @param MethodProphecy $method
     *
     * @throws PredictionException
     * @return void
     */
    public function check(array $calls, ObjectProphecy $object, MethodProphecy $method);
}
