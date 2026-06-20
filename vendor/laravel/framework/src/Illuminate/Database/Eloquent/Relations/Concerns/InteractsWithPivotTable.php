<?php
/**
 * Illuminate，数据库，Eloquent，关联，问题，与数据透视表交互
 */

namespace Illuminate\Database\Eloquent\Relations\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

trait InteractsWithPivotTable
{
    /**
     * Toggles a model (or models) from the parent.
	 * 从父模型切换一个（或多个）模型。
     *
     * Each existing model is detached, and non existing ones are attached.
	 * 每个现有的模型是分离的，而不存在的模型是附加的。
     *
     * @param  mixed  $ids
     * @param  bool   $touch
     * @return array
     */
    public function toggle($ids, $touch = true)
    {
        $changes = [
            'attached' => [], 'detached' => [],
        ];

        $records = $this->formatRecordsList($this->parseIds($ids));

        // Next, we will determine which IDs should get removed from the join table by
        // checking which of the given ID/records is in the list of current records
        // and removing all of those rows from this "intermediate" joining table.
		// 接下来,我们将通过检查给定ID /记录中的哪个是当前记录的列表,
		// 并从这个“中间”连接表中删除所有这些行,我们将确定哪些ID应该从join表中删除。
        $detach = array_values(array_intersect(
            $this->newPivotQuery()->pluck($this->relatedPivotKey)->all(),
            array_keys($records)
        ));

        if (count($detach) > 0) {
            $this->detach($detach, false);

            $changes['detached'] = $this->castKeys($detach);
        }

        // Finally, for all of the records which were not "detached", we'll attach the
        // records into the intermediate table. Then, we will add those attaches to
        // this change list and get ready to return these results to the callers.
		// 最后，对于所有未被“分离”的记录，我们将把它们附加到中间表中。
		// 然后，将这些附加操作添加到变更列表中，并准备将结果返回给调用者。
        $attach = array_diff_key($records, array_flip($detach));

        if (count($attach) > 0) {
            $this->attach($attach, [], false);

            $changes['attached'] = array_keys($attach);
        }

        // Once we have finished attaching or detaching the records, we will see if we
        // have done any attaching or detaching, and if we have we will touch these
        // relationships if they are configured to touch on any database updates.
		// 在完成记录的附加或分离操作后，我们将检查是否进行了任何附加或分离操作；
		// 如果进行了，则会根据配置对这些关系进行处理，以响应数据库的更新。
        if ($touch && (count($changes['attached']) ||
                       count($changes['detached']))) {
            $this->touchIfTouching();
        }

        return $changes;
    }

    /**
     * Sync the intermediate tables with a list of IDs without detaching.
	 * 同步中间表与id列表，而不分离。
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array  $ids
     * @return array
     */
    public function syncWithoutDetaching($ids)
    {
        return $this->sync($ids, false);
    }

    /**
     * Sync the intermediate tables with a list of IDs or collection of models.
	 * 将中间表与id列表或模型集合同步
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array  $ids
     * @param  bool   $detaching
     * @return array
     */
    public function sync($ids, $detaching = true)
    {
        $changes = [
            'attached' => [], 'detached' => [], 'updated' => [],
        ];

        // First we need to attach any of the associated models that are not currently
        // in this joining table. We'll spin through the given IDs, checking to see
        // if they exist in the array of current ones, and if not we will insert.
		// 首先，我们需要将当前未在该关联表中的相关模型附加进来。
		// 我们将遍历给定的ID，检查它们是否存在于当前已有的数组中，如果不存在，则进行插入。
        $current = $this->newPivotQuery()->pluck(
            $this->relatedPivotKey
        )->all();

        $detach = array_diff($current, array_keys(
            $records = $this->formatRecordsList($this->parseIds($ids))
        ));

        // Next, we will take the differences of the currents and given IDs and detach
        // all of the entities that exist in the "current" array but are not in the
        // array of the new IDs given to the method which will complete the sync.
		// 接下来，我们将取当前电流和给定ID的差异，
		// 并移除“当前”数组中所有存在于新方法传入的ID数组之外的实体，以完成同步。
        if ($detaching && count($detach) > 0) {
            $this->detach($detach);

            $changes['detached'] = $this->castKeys($detach);
        }

        // Now we are finally ready to attach the new records. Note that we'll disable
        // touching until after the entire operation is complete so we don't fire a
        // ton of touch operations until we are totally done syncing the records.
		// 现在我们终于可以开始添加新的记录了。请注意，我们将暂停触摸操作，
		// 直到整个操作完成，这样在完全同步完所有记录之前就不会触发大量触摸操作。
        $changes = array_merge(
            $changes, $this->attachNew($records, $current, false)
        );

        // Once we have finished attaching or detaching the records, we will see if we
        // have done any attaching or detaching, and if we have we will touch these
        // relationships if they are configured to touch on any database updates.
		// 在完成记录的附加或分离操作后，我们将检查是否进行了任何附加或分离操作；
		// 如果进行了，则会根据配置对这些关系进行处理，以响应数据库的更新。
        if (count($changes['attached']) ||
            count($changes['updated'])) {
            $this->touchIfTouching();
        }

        return $changes;
    }

    /**
     * Format the sync / toggle record list so that it is keyed by ID.
	 * 格式化同步/切换记录列表，使其按ID键。
     *
     * @param  array  $records
     * @return array
     */
    protected function formatRecordsList(array $records)
    {
        return collect($records)->mapWithKeys(function ($attributes, $id) {
            if (! is_array($attributes)) {
                [$id, $attributes] = [$attributes, []];
            }

            return [$id => $attributes];
        })->all();
    }

    /**
     * Attach all of the records that aren't in the given current records.
	 * 附加所有不在给定当前记录中的记录
     *
     * @param  array  $records
     * @param  array  $current
     * @param  bool   $touch
     * @return array
     */
    protected function attachNew(array $records, array $current, $touch = true)
    {
        $changes = ['attached' => [], 'updated' => []];

        foreach ($records as $id => $attributes) {
            // If the ID is not in the list of existing pivot IDs, we will insert a new pivot
            // record, otherwise, we will just update this existing record on this joining
            // table, so that the developers will easily update these records pain free.
			// 如果ID不在现有枢轴ID列表中，我们将插入一条新的枢轴记录；
			// 否则，我们只需更新该连接表中的现有记录，以便开发人员能够轻松、无痛地修改这些记录。
            if (! in_array($id, $current)) {
                $this->attach($id, $attributes, $touch);

                $changes['attached'][] = $this->castKey($id);
            }

            // Now we'll try to update an existing pivot record with the attributes that were
            // given to the method. If the model is actually updated we will add it to the
            // list of updated pivot records so we return them back out to the consumer.
			// 现在我们将尝试使用传递给方法的属性来更新现有的关联记录。
			// 如果模型确实被更新，我们就会将其添加到已更新的关联记录列表中，以便返回给消费者。
            elseif (count($attributes) > 0 &&
                $this->updateExistingPivot($id, $attributes, $touch)) {
                $changes['updated'][] = $this->castKey($id);
            }
        }

        return $changes;
    }

    /**
     * Update an existing pivot record on the table.
	 * 更新表上现有的数据透视记录
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool   $touch
     * @return int
     */
    public function updateExistingPivot($id, array $attributes, $touch = true)
    {
        if (in_array($this->updatedAt(), $this->pivotColumns)) {
            $attributes = $this->addTimestampsToAttachment($attributes, true);
        }

        $updated = $this->newPivotStatementForId($this->parseId($id))->update(
            $this->castAttributes($attributes)
        );

        if ($touch) {
            $this->touchIfTouching();
        }

        return $updated;
    }

    /**
     * Attach a model to the parent.
	 * 将一个模型附加到父模型上
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool   $touch
     * @return void
     */
    public function attach($id, array $attributes = [], $touch = true)
    {
        // Here we will insert the attachment records into the pivot table. Once we have
        // inserted the records, we will touch the relationships if necessary and the
        // function will return. We can parse the IDs before inserting the records.
		// 我们将把附件记录插入数据透视表中。插入记录后，如有需要，会处理相关关系，
		// 然后函数返回。在插入记录之前，我们可以解析ID。
        $this->newPivotStatement()->insert($this->formatAttachRecords(
            $this->parseIds($id), $attributes
        ));

        if ($touch) {
            $this->touchIfTouching();
        }
    }

    /**
     * Create an array of records to insert into the pivot table.
	 * 创建要插入数据透视表的记录数组
     *
     * @param  array  $ids
     * @param  array  $attributes
     * @return array
     */
    protected function formatAttachRecords($ids, array $attributes)
    {
        $records = [];

        $hasTimestamps = ($this->hasPivotColumn($this->createdAt()) ||
                  $this->hasPivotColumn($this->updatedAt()));

        // To create the attachment records, we will simply spin through the IDs given
        // and create a new record to insert for each ID. Each ID may actually be a
        // key in the array, with extra attributes to be placed in other columns.
		// 为了创建附件记录，我们只需遍历给定的ID，并为每个ID创建一个新的插入记录。
		// 每个ID实际上可能都是数组中的一个键，而其他属性则会放入其他列中。
        foreach ($ids as $key => $value) {
            $records[] = $this->formatAttachRecord(
                $key, $value, $attributes, $hasTimestamps
            );
        }

        return $records;
    }

    /**
     * Create a full attachment record payload.
	 * 创建一个完整的附件记录有效负载
     *
     * @param  int    $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @param  bool   $hasTimestamps
     * @return array
     */
    protected function formatAttachRecord($key, $value, $attributes, $hasTimestamps)
    {
        [$id, $attributes] = $this->extractAttachIdAndAttributes($key, $value, $attributes);

        return array_merge(
            $this->baseAttachRecord($id, $hasTimestamps), $this->castAttributes($attributes)
        );
    }

    /**
     * Get the attach record ID and extra attributes.
	 * 获取附加记录ID和额外属性
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    protected function extractAttachIdAndAttributes($key, $value, array $attributes)
    {
        return is_array($value)
                    ? [$key, array_merge($value, $attributes)]
                    : [$value, $attributes];
    }

    /**
     * Create a new pivot attachment record.
	 * 创建一个新的枢轴附件记录
     *
     * @param  int   $id
     * @param  bool  $timed
     * @return array
     */
    protected function baseAttachRecord($id, $timed)
    {
        $record[$this->relatedPivotKey] = $id;

        $record[$this->foreignPivotKey] = $this->parent->{$this->parentKey};

        // If the record needs to have creation and update timestamps, we will make
        // them by calling the parent model's "freshTimestamp" method which will
        // provide us with a fresh timestamp in this model's preferred format.
		// 如果记录需要包含创建和更新时间戳，我们将通过调用父模型的“freshTimestamp”方法来生成，
		// 该方法会以本模型首选的格式为我们提供一个全新的时间戳。
        if ($timed) {
            $record = $this->addTimestampsToAttachment($record);
        }

        foreach ($this->pivotValues as $value) {
            $record[$value['column']] = $value['value'];
        }

        return $record;
    }

    /**
     * Set the creation and update timestamps on an attach record.
	 * 设置附加记录上的创建和更新时间戳
     *
     * @param  array  $record
     * @param  bool   $exists
     * @return array
     */
    protected function addTimestampsToAttachment(array $record, $exists = false)
    {
        $fresh = $this->parent->freshTimestamp();

        if ($this->using) {
            $pivotModel = new $this->using;

            $fresh = $fresh->format($pivotModel->getDateFormat());
        }

        if (! $exists && $this->hasPivotColumn($this->createdAt())) {
            $record[$this->createdAt()] = $fresh;
        }

        if ($this->hasPivotColumn($this->updatedAt())) {
            $record[$this->updatedAt()] = $fresh;
        }

        return $record;
    }

    /**
     * Determine whether the given column is defined as a pivot column.
	 * 确定给定列是否定义为主列
     *
     * @param  string  $column
     * @return bool
     */
    protected function hasPivotColumn($column)
    {
        return in_array($column, $this->pivotColumns);
    }

    /**
     * Detach models from the relationship.
	 * 从关系中分离模型
     *
     * @param  mixed  $ids
     * @param  bool  $touch
     * @return int
     */
    public function detach($ids = null, $touch = true)
    {
        $query = $this->newPivotQuery();

        // If associated IDs were passed to the method we will only delete those
        // associations, otherwise all of the association ties will be broken.
        // We'll return the numbers of affected rows when we do the deletes.
		// 如果向方法传递了关联ID，我们只会删除这些关联；否则所有关联都将被断开。
		// 执行删除操作时，我们将返回受影响的行数。
        if (! is_null($ids)) {
            $ids = $this->parseIds($ids);

            if (empty($ids)) {
                return 0;
            }

            $query->whereIn($this->relatedPivotKey, (array) $ids);
        }

        // Once we have all of the conditions set on the statement, we are ready
        // to run the delete on the pivot table. Then, if the touch parameter
        // is true, we will go ahead and touch all related models to sync.
		// 在设置完所有条件后，我们就可以执行数据透视表的删除操作。
		// 然后，如果 touch 参数为真，我们将继续同步所有相关模型。
        $results = $query->delete();

        if ($touch) {
            $this->touchIfTouching();
        }

        return $results;
    }

    /**
     * Create a new pivot model instance.
	 * 创建一个新的pivot模型实例
     *
     * @param  array  $attributes
     * @param  bool   $exists
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(array $attributes = [], $exists = false)
    {
        $pivot = $this->related->newPivot(
            $this->parent, $attributes, $this->table, $exists, $this->using
        );

        return $pivot->setPivotKeys($this->foreignPivotKey, $this->relatedPivotKey);
    }

    /**
     * Create a new existing pivot model instance.
	 * 创建一个新的现有pivot模型实例
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     */
    public function newExistingPivot(array $attributes = [])
    {
        return $this->newPivot($attributes, true);
    }

    /**
     * Get a new plain query builder for the pivot table.
	 * 为数据透视表获取一个新的普通查询生成器
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function newPivotStatement()
    {
        return $this->query->getQuery()->newQuery()->from($this->table);
    }

    /**
     * Get a new pivot statement for a given "other" ID.
	 * 获取给定“other”ID的新枢轴语句
     *
     * @param  mixed  $id
     * @return \Illuminate\Database\Query\Builder
     */
    public function newPivotStatementForId($id)
    {
        return $this->newPivotQuery()->whereIn($this->relatedPivotKey, $this->parseIds($id));
    }

    /**
     * Create a new query builder for the pivot table.
	 * 为数据透视表创建一个新的查询生成器
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newPivotQuery()
    {
        $query = $this->newPivotStatement();

        foreach ($this->pivotWheres as $arguments) {
            call_user_func_array([$query, 'where'], $arguments);
        }

        foreach ($this->pivotWhereIns as $arguments) {
            call_user_func_array([$query, 'whereIn'], $arguments);
        }

        return $query->where($this->foreignPivotKey, $this->parent->{$this->parentKey});
    }

    /**
     * Set the columns on the pivot table to retrieve.
	 * 设置数据透视表上要检索的列
     *
     * @param  array|mixed  $columns
     * @return $this
     */
    public function withPivot($columns)
    {
        $this->pivotColumns = array_merge(
            $this->pivotColumns, is_array($columns) ? $columns : func_get_args()
        );

        return $this;
    }

    /**
     * Get all of the IDs from the given mixed value.
	 * 从给定的混合值中获取所有id
     *
     * @param  mixed  $value
     * @return array
     */
    protected function parseIds($value)
    {
        if ($value instanceof Model) {
            return [$value->{$this->relatedKey}];
        }

        if ($value instanceof Collection) {
            return $value->pluck($this->relatedKey)->all();
        }

        if ($value instanceof BaseCollection) {
            return $value->toArray();
        }

        return (array) $value;
    }

    /**
     * Get the ID from the given mixed value.
	 * 从给定的混合值中获取ID
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function parseId($value)
    {
        return $value instanceof Model ? $value->{$this->relatedKey} : $value;
    }

    /**
     * Cast the given keys to integers if they are numeric and string otherwise.
	 * 如果给定的键是数字，则将其转换为整数，否则将其转换为字符串。
     *
     * @param  array  $keys
     * @return array
     */
    protected function castKeys(array $keys)
    {
        return array_map(function ($v) {
            return $this->castKey($v);
        }, $keys);
    }

    /**
     * Cast the given key to convert to primary key type.
	 * 强制转换给定的键以转换为主键类型
     *
     * @param  mixed  $key
     * @return mixed
     */
    protected function castKey($key)
    {
        return $this->getTypeSwapValue(
            $this->related->getKeyType(),
            $key
        );
    }

    /**
     * Cast the given pivot attributes.
	 * 转换给定的枢轴属性
     *
     * @param  array $attributes
     * @return array
     */
    protected function castAttributes($attributes)
    {
        return $this->using
                    ? $this->newPivot()->fill($attributes)->getAttributes()
                    : $attributes;
    }

    /**
     * Converts a given value to a given type value.
	 * 将给定值转换为给定类型值
     *
     * @param  string $type
     * @param  mixed  $value
     * @return mixed
     */
    protected function getTypeSwapValue($type, $value)
    {
        switch (strtolower($type)) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            default:
                return $value;
        }
    }
}
