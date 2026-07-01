<?php
/**
 * Carbon，主义，日期时间类型
 */

namespace Carbon\Doctrine;

use Carbon\Carbon;
use Doctrine\DBAL\Types\VarDateTimeType;

class DateTimeType extends VarDateTimeType implements CarbonDoctrineType
{
    /** @use CarbonTypeConverter<Carbon> */
    use CarbonTypeConverter;
}
