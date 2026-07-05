<?php
/**
 * Monolog，处理程序，什么失败组处理程序
 */

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

/**
 * Forwards records to multiple handlers suppressing failures of each handler
 * and continuing through to give every handler a chance to succeed.
 * 将记录转发给多个处理程序,抑制每个处理程序的失败,并继续给每个处理程序一个成功的机会。
 *
 * @author Craig D'Amelio <craig@damelio.ca>
 */
class WhatFailureGroupHandler extends GroupHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        if ($this->processors) {
            foreach ($this->processors as $processor) {
                $record = call_user_func($processor, $record);
            }
        }

        foreach ($this->handlers as $handler) {
            try {
                $handler->handle($record);
            } catch (\Exception $e) {
                // What failure?
            } catch (\Throwable $e) {
                // What failure?
            }
        }

        return false === $this->bubble;
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        if ($this->processors) {
            $processed = array();
            foreach ($records as $record) {
                foreach ($this->processors as $processor) {
                    $record = call_user_func($processor, $record);
                }
                $processed[] = $record;
            }
            $records = $processed;
        }

        foreach ($this->handlers as $handler) {
            try {
                $handler->handleBatch($records);
            } catch (\Exception $e) {
                // What failure?
            } catch (\Throwable $e) {
                // What failure?
            }
        }
    }
}
