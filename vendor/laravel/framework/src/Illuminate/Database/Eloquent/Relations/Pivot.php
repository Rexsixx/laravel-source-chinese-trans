<?php
/**
 * Illuminate，数据库，Eloquent，关系，Pivot
 */

namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class Pivot extends Model
{
    use AsPivot;

    /**
     * The attributes that aren't mass assignable.
	 * 不能大规模分配的属性
     *
     * @var array
     */
    protected $guarded = [];
}
