<?php
/**
 * Cron，字段工厂
 */

namespace Cron;

use InvalidArgumentException;

/**
 * CRON field factory implementing a flyweight factory
 * CRON字段工厂实施flyweight工厂
 * @link http://en.wikipedia.org/wiki/Cron
 */
class FieldFactory
{
    /**
     * @var array Cache of instantiated fields
     */
    private $fields = array();

    /**
     * Get an instance of a field object for a cron expression position
	 * 为cron表达式位置获取字段对象的实例
     *
     * @param int $position CRON expression position value to retrieve
     *
     * @return FieldInterface
     * @throws InvalidArgumentException if a position is not valid
     */
    public function getField($position)
    {
        if (!isset($this->fields[$position])) {
            switch ($position) {
                case 0:
                    $this->fields[$position] = new MinutesField();
                    break;
                case 1:
                    $this->fields[$position] = new HoursField();
                    break;
                case 2:
                    $this->fields[$position] = new DayOfMonthField();
                    break;
                case 3:
                    $this->fields[$position] = new MonthField();
                    break;
                case 4:
                    $this->fields[$position] = new DayOfWeekField();
                    break;
                default:
                    throw new InvalidArgumentException(
                        ($position + 1) . ' is not a valid position'
                    );
            }
        }

        return $this->fields[$position];
    }
}
