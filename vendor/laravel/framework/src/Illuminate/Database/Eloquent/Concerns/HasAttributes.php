<?php
/**
 * Illuminate，数据库，Eloquent，问题，有属性
 */

namespace Illuminate\Database\Eloquent\Concerns;

use LogicException;
use DateTimeInterface;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Database\Eloquent\JsonEncodingException;

trait HasAttributes
{
    /**
     * The model's attributes.
	 * 模型的属性
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The model attribute's original state.
	 * 模型属性的原始状态
     *
     * @var array
     */
    protected $original = [];

    /**
     * The changed model attributes.
	 * 更改的模型属性
     *
     * @var array
     */
    protected $changes = [];

    /**
     * The attributes that should be cast to native types.
	 * 应该转换为本机类型的属性
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that should be mutated to dates.
	 * 应该被更改为日期的属性
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The storage format of the model's date columns.
	 * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat;

    /**
     * The accessors to append to the model's array form.
	 * 附加到模型数组形式的访问器
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Indicates whether attributes are snake cased on arrays.
	 * 指示属性是否在数组上使用蛇形大小写
     *
     * @var bool
     */
    public static $snakeAttributes = true;

    /**
     * The cache of the mutated attributes for each class.
	 * 每个类的突变属性的缓存
     *
     * @var array
     */
    protected static $mutatorCache = [];

    /**
     * Convert the model's attributes to an array.
	 * 将模型的属性转换为数组
     *
     * @return array
     */
    public function attributesToArray()
    {
        // If an attribute is a date, we will cast it to a string after converting it
        // to a DateTime / Carbon instance. This is so we will get some consistent
        // formatting while accessing attributes vs. arraying / JSONing a model.
		// 如果某个属性是日期类型，那么在将其转换为“DateTime”/“Carbon”实例之后，我们会将其转换为字符串形式。
		// 这样做是为了在访问属性以及对模型进行数组化/JSON化操作时，能够获得一致的格式。
        $attributes = $this->addDateAttributesToArray(
            $attributes = $this->getArrayableAttributes()
        );

        $attributes = $this->addMutatedAttributesToArray(
            $attributes, $mutatedAttributes = $this->getMutatedAttributes()
        );

        // Next we will handle any casts that have been setup for this model and cast
        // the values to their appropriate type. If the attribute has a mutator we
        // will not perform the cast on those attributes to avoid any confusion.
		// 接下来，我们将处理为该模型设置的所有转换操作，并将值转换为相应的类型。
		// 如果该属性具有修改器，我们则不会对这些属性进行类型转换，以避免造成混淆。
        $attributes = $this->addCastAttributesToArray(
            $attributes, $mutatedAttributes
        );

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.
		// 在这里，我们将获取此模型所附加的所有计算属性，因为这些属性实际上并不在属性数组中，
		// 但在我们需要将模型转换为数组或 JSON 格式以方便给编码人员使用时，会运行这些属性。
        foreach ($this->getArrayableAppends() as $key) {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }

        return $attributes;
    }

    /**
     * Add the date attributes to the attributes array.
	 * 将日期属性添加到属性数组中
     *
     * @param  array  $attributes
     * @return array
     */
    protected function addDateAttributesToArray(array $attributes)
    {
        foreach ($this->getDates() as $key) {
            if (! isset($attributes[$key])) {
                continue;
            }

            $attributes[$key] = $this->serializeDate(
                $this->asDateTime($attributes[$key])
            );
        }

        return $attributes;
    }

    /**
     * Add the mutated attributes to the attributes array.
	 * 将突变的属性添加到属性数组中
     *
     * @param  array  $attributes
     * @param  array  $mutatedAttributes
     * @return array
     */
    protected function addMutatedAttributesToArray(array $attributes, array $mutatedAttributes)
    {
        foreach ($mutatedAttributes as $key) {
            // We want to spin through all the mutated attributes for this model and call
            // the mutator for the attribute. We cache off every mutated attributes so
            // we don't have to constantly check on attributes that actually change.
			// 我们要遍历这个模型的所有变异属性，并调用该属性的变异器。
			// 我们会对所有发生变化的属性进行缓存处理，这样就不必持续检查那些实际发生了变化的属性了。
            if (! array_key_exists($key, $attributes)) {
                continue;
            }

            // Next, we will call the mutator for this attribute so that we can get these
            // mutated attribute's actual values. After we finish mutating each of the
            // attributes we will return this final array of the mutated attributes.
			// 接下来，我们将调用此属性的修改器，以便获取这些经过修改后的属性的实际值。
			// 在我们完成对每个属性的变异操作后，将会返回这个包含变异后属性的最终数组。
            $attributes[$key] = $this->mutateAttributeForArray(
                $key, $attributes[$key]
            );
        }

        return $attributes;
    }

    /**
     * Add the casted attributes to the attributes array.
	 * 将转换属性添加到属性数组中
     *
     * @param  array  $attributes
     * @param  array  $mutatedAttributes
     * @return array
     */
    protected function addCastAttributesToArray(array $attributes, array $mutatedAttributes)
    {
        foreach ($this->getCasts() as $key => $value) {
            if (! array_key_exists($key, $attributes) || in_array($key, $mutatedAttributes)) {
                continue;
            }

            // Here we will cast the attribute. Then, if the cast is a date or datetime cast
            // then we will serialize the date for the array. This will convert the dates
            // to strings based on the date format specified for these Eloquent models.
			// 接下来我们将进行属性的转换。然后，如果转换的是日期或日期时间类型的转换，那么我们将为数组中的日期进行序列化处理。
			// 这将根据为这些 Eloquent 模型指定的日期格式将日期转换为字符串。
            $attributes[$key] = $this->castAttribute(
                $key, $attributes[$key]
            );

            // If the attribute cast was a date or a datetime, we will serialize the date as
            // a string. This allows the developers to customize how dates are serialized
            // into an array without affecting how they are persisted into the storage.
			// 如果属性“cast”是指定的日期或日期时间类型，我们将把该日期转换为字符串进行序列化。
			// 这使得开发人员能够自行设定将日期序列化为数组的方式，而不会影响其在存储中的保存方式。
            if ($attributes[$key] &&
                ($value === 'date' || $value === 'datetime')) {
                $attributes[$key] = $this->serializeDate($attributes[$key]);
            }

            if ($attributes[$key] && $this->isCustomDateTimeCast($value)) {
                $attributes[$key] = $attributes[$key]->format(explode(':', $value, 2)[1]);
            }
        }

        return $attributes;
    }

    /**
     * Get an attribute array of all arrayable attributes.
	 * 获取包含所有可数组属性的属性数组
     *
     * @return array
     */
    protected function getArrayableAttributes()
    {
        return $this->getArrayableItems($this->attributes);
    }

    /**
     * Get all of the appendable values that are arrayable.
	 * 获取所有可数组的可追加值
     *
     * @return array
     */
    protected function getArrayableAppends()
    {
        if (! count($this->appends)) {
            return [];
        }

        return $this->getArrayableItems(
            array_combine($this->appends, $this->appends)
        );
    }

    /**
     * Get the model's relationships in array form.
	 * 以数组形式获取模型的关系
     *
     * @return array
     */
    public function relationsToArray()
    {
        $attributes = [];

        foreach ($this->getArrayableRelations() as $key => $value) {
            // If the values implements the Arrayable interface we can just call this
            // toArray method on the instances which will convert both models and
            // collections to their proper array form and we'll set the values.
			// 如果这些值实现了“可数组化”接口，我们就可以直接在这些实例上调用“toArray”方法，
			// 该方法会将模型和集合转换为正确的数组形式，并且还会设置这些值。
            if ($value instanceof Arrayable) {
                $relation = $value->toArray();
            }

            // If the value is null, we'll still go ahead and set it in this list of
            // attributes since null is used to represent empty relationships if
            // if it a has one or belongs to type relationships on the models.
			// 如果该值为空，则我们仍将在此属性列表中为其设置一个值，因为在模型中的“一”关系或“属于”关系中，空值用于表示空的关联关系。
            elseif (is_null($value)) {
                $relation = $value;
            }

            // If the relationships snake-casing is enabled, we will snake case this
            // key so that the relation attribute is snake cased in this returned
            // array to the developers, making this consistent with attributes.
			// 如果启用了关系的蛇形命名方式，我们将对这个键进行蛇形转换，以便在返回给开发人员的数组中，
			// 该关系属性也能采用蛇形命名方式，从而使其与属性的命名保持一致。
            if (static::$snakeAttributes) {
                $key = Str::snake($key);
            }

            // If the relation value has been set, we will set it on this attributes
            // list for returning. If it was not arrayable or null, we'll not set
            // the value on the array because it is some type of invalid value.
			// 如果已设置了关系值，我们将将其设置在这组属性列表中以便返回。
			// 如果该值既不是数组类型也不是空值，我们就不会将该值设置到数组中，因为这属于某种无效值。
            if (isset($relation) || is_null($value)) {
                $attributes[$key] = $relation;
            }

            unset($relation);
        }

        return $attributes;
    }

    /**
     * Get an attribute array of all arrayable relations.
	 * 获取所有可数组关系的属性数组
     *
     * @return array
     */
    protected function getArrayableRelations()
    {
        return $this->getArrayableItems($this->relations);
    }

    /**
     * Get an attribute array of all arrayable values.
	 * 获取所有可数组值的属性数组
     *
     * @param  array  $values
     * @return array
     */
    protected function getArrayableItems(array $values)
    {
        if (count($this->getVisible()) > 0) {
            $values = array_intersect_key($values, array_flip($this->getVisible()));
        }

        if (count($this->getHidden()) > 0) {
            $values = array_diff_key($values, array_flip($this->getHidden()));
        }

        return $values;
    }

    /**
     * Get an attribute from the model.
	 * 从模型中获取一个属性
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (! $key) {
            return;
        }

        // If the attribute exists in the attribute array or has a "get" mutator we will
        // get the attribute's value. Otherwise, we will proceed as if the developers
        // are asking for a relationship's value. This covers both types of values.
		// 如果该属性存在于属性数组中，或者具有“获取”修改器，我们将获取该属性的值。
		// 否则，我们将按照开发者要求获取关系值的方式继续处理。
        if (array_key_exists($key, $this->attributes) ||
            $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }

        // Here we will determine if the model base class itself contains this given key
        // since we don't want to treat any of those methods as relationships because
        // they are all intended as helper methods and none of these are relations.
		// 在此，我们将判断模型基类自身是否包含该给定的键，因为我们不想将这些方法中的任何一种视为关系，
		// 因为它们都只是辅助方法，而且这些都不是关系。
        if (method_exists(self::class, $key)) {
            return;
        }

        return $this->getRelationValue($key);
    }

    /**
     * Get a plain attribute (not a relationship).
	 * 获取普通属性（而不是关系）
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        $value = $this->getAttributeFromArray($key);

        // If the attribute has a get mutator, we will call that then return what
        // it returns as the value, which is useful for transforming values on
        // retrieval from the model to a form that is more useful for usage.
		// 如果该属性具有获取器方法，我们将调用该方法，并将其返回的值作为结果值返回，
		// 这样便于在从模型中检索值时将其转换为更便于使用的格式。
        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }

        // If the attribute exists within the cast array, we will convert it to
        // an appropriate native PHP type dependant upon the associated value
        // given with the key in the pair. Dayle made this comment line up.
		// 如果该属性存在于数组中，我们将根据键对中与之关联的值，将其转换为相应的原生 PHP 类型。
        if ($this->hasCast($key)) {
            return $this->castAttribute($key, $value);
        }

        // If the attribute is listed as a date, we will convert it to a DateTime
        // instance on retrieval, which makes it quite convenient to work with
        // date fields without having to create a mutator for each property.
		// 如果该属性被指定为日期类型，那么在检索时我们将将其转换为一个 DateTime 实例，
		// 这样就使得处理日期字段变得非常方便，而无需为每个属性都创建一个修改器。
        if (in_array($key, $this->getDates()) &&
            ! is_null($value)) {
            return $this->asDateTime($value);
        }

        return $value;
    }

    /**
     * Get an attribute from the $attributes array.
	 * 从$attributes数组中获取一个属性
     *
     * @param  string  $key
     * @return mixed
     */
    protected function getAttributeFromArray($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Get a relationship.
	 * 建立一段关系
     *
     * @param  string  $key
     * @return mixed
     */
    public function getRelationValue($key)
    {
        // If the key already exists in the relationships array, it just means the
        // relationship has already been loaded, so we'll just return it out of
        // here because there is no need to query within the relations twice.
		// 如果该键已在“关系”数组中存在，这意味着该关系已经加载完成，
		// 所以我们就直接从这里返回它，因为没有必要在关系中重复查询两次。
        if ($this->relationLoaded($key)) {
            return $this->relations[$key];
        }

        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return results from the query
        // and hydrate the relationship's value on the "relationships" array.
		// 如果“属性”是以方法的形式存在于模型中的，我们将假定它是一种关系，
		// 并会加载并返回查询结果，并在“关系”数组中填充该关系的值。
        if (method_exists($this, $key)) {
            return $this->getRelationshipFromMethod($key);
        }
    }

    /**
     * Get a relationship value from a method.
	 * 从方法获取关系值
     *
     * @param  string  $method
     * @return mixed
     *
     * @throws \LogicException
     */
    protected function getRelationshipFromMethod($method)
    {
        $relation = $this->$method();

        if (! $relation instanceof Relation) {
            if (is_null($relation)) {
                throw new LogicException(sprintf(
                    '%s::%s must return a relationship instance, but "null" was returned. Was the "return" keyword used?', static::class, $method
                ));
            }

            throw new LogicException(sprintf(
                '%s::%s must return a relationship instance.', static::class, $method
            ));
        }

        return tap($relation->getResults(), function ($results) use ($method) {
            $this->setRelation($method, $results);
        });
    }

    /**
     * Determine if a get mutator exists for an attribute.
	 * 确定属性是否存在get mutator
     *
     * @param  string  $key
     * @return bool
     */
    public function hasGetMutator($key)
    {
        return method_exists($this, 'get'.Str::studly($key).'Attribute');
    }

    /**
     * Get the value of an attribute using its mutator.
	 * 使用属性的赋值器获取属性的值
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function mutateAttribute($key, $value)
    {
        return $this->{'get'.Str::studly($key).'Attribute'}($value);
    }

    /**
     * Get the value of an attribute using its mutator for array conversion.
	 * 使用属性的赋值器获取属性的值，以便进行数组转换。
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function mutateAttributeForArray($key, $value)
    {
        $value = $this->mutateAttribute($key, $value);

        return $value instanceof Arrayable ? $value->toArray() : $value;
    }

    /**
     * Cast an attribute to a native PHP type.
	 * 将属性强制转换为本机PHP类型
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($this->getCastType($key)) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return $this->fromFloat($value);
            case 'decimal':
                return $this->asDecimal($value, explode(':', $this->getCasts()[$key], 2)[1]);
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'collection':
                return new BaseCollection($this->fromJson($value));
            case 'date':
                return $this->asDate($value);
            case 'datetime':
            case 'custom_datetime':
                return $this->asDateTime($value);
            case 'timestamp':
                return $this->asTimestamp($value);
            default:
                return $value;
        }
    }

    /**
     * Get the type of cast for a model attribute.
	 * 获取模型属性的强制转换类型
     *
     * @param  string  $key
     * @return string
     */
    protected function getCastType($key)
    {
        if ($this->isCustomDateTimeCast($this->getCasts()[$key])) {
            return 'custom_datetime';
        }

        if ($this->isDecimalCast($this->getCasts()[$key])) {
            return 'decimal';
        }

        return trim(strtolower($this->getCasts()[$key]));
    }

    /**
     * Determine if the cast type is a custom date time cast.
	 * 确定转换类型是否为自定义日期时间转换
     *
     * @param  string  $cast
     * @return bool
     */
    protected function isCustomDateTimeCast($cast)
    {
        return strncmp($cast, 'date:', 5) === 0 ||
               strncmp($cast, 'datetime:', 9) === 0;
    }

    /**
     * Determine if the cast type is a decimal cast.
	 * 确定转换类型是否为小数类型转换
     *
     * @param  string  $cast
     * @return bool
     */
    protected function isDecimalCast($cast)
    {
        return strncmp($cast, 'decimal:', 8) === 0;
    }

    /**
     * Set a given attribute on the model.
	 * 在模型上设置给定的属性
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set on
        // the model, such as "json_encoding" an listing of data for storage.
		// 首先，我们将检查是否存在用于集合操作的修改器，
		// 该修改器能让开发人员在模型中设置属性时对其进行调整，例如“json_encoding”（用于存储数据的列表）。
        if ($this->hasSetMutator($key)) {
            return $this->setMutatedAttributeValue($key, $value);
        }

        // If an attribute is listed as a "date", we'll convert it from a DateTime
        // instance into a form proper for storage on the database tables using
        // the connection grammar's date format. We will auto set the values.
		// 如果某个属性被标记为“日期”，我们将将其从“DateTime”实例转换为适合存储在数据库表中的格式，转换过程将依据连接语法中的日期格式进行。
        elseif ($value && $this->isDateAttribute($key)) {
            $value = $this->fromDateTime($value);
        }

        if ($this->isJsonCastable($key) && ! is_null($value)) {
            $value = $this->castAttributeAsJson($key, $value);
        }

        // If this attribute contains a JSON ->, we'll set the proper value in the
        // attribute's underlying array. This takes care of properly nesting an
        // attribute in the array's value in the case of deeply nested items.
		// 如果此属性包含“JSON ->”这一内容，我们将为其底层数组设置相应的值。
        if (Str::contains($key, '->')) {
            return $this->fillJsonAttribute($key, $value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Determine if a set mutator exists for an attribute.
	 * 确定是否存在属性的集合赋值器
     *
     * @param  string  $key
     * @return bool
     */
    public function hasSetMutator($key)
    {
        return method_exists($this, 'set'.Str::studly($key).'Attribute');
    }

    /**
     * Set the value of an attribute using its mutator.
	 * 使用属性的赋值器设置属性的值
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function setMutatedAttributeValue($key, $value)
    {
        return $this->{'set'.Str::studly($key).'Attribute'}($value);
    }

    /**
     * Determine if the given attribute is a date or date castable.
	 * 确定给定的属性是日期还是日期浇注表
     *
     * @param  string  $key
     * @return bool
     */
    protected function isDateAttribute($key)
    {
        return in_array($key, $this->getDates(), true) ||
                                    $this->isDateCastable($key);
    }

    /**
     * Set a given JSON attribute on the model.
	 * 在模型上设置一个给定的JSON属性
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function fillJsonAttribute($key, $value)
    {
        [$key, $path] = explode('->', $key, 2);

        $this->attributes[$key] = $this->asJson($this->getArrayAttributeWithValue(
            $path, $key, $value
        ));

        return $this;
    }

    /**
     * Get an array attribute with the given key and value set.
	 * 获取具有给定键和值集的数组属性
     *
     * @param  string  $path
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    protected function getArrayAttributeWithValue($path, $key, $value)
    {
        return tap($this->getArrayAttributeByKey($key), function (&$array) use ($path, $value) {
            Arr::set($array, str_replace('->', '.', $path), $value);
        });
    }

    /**
     * Get an array attribute or return an empty array if it is not set.
	 * 获取数组属性，如果未设置则返回空数组。
     *
     * @param  string  $key
     * @return array
     */
    protected function getArrayAttributeByKey($key)
    {
        return isset($this->attributes[$key]) ?
                    $this->fromJson($this->attributes[$key]) : [];
    }

    /**
     * Cast the given attribute to JSON.
	 * 将给定的属性转换为JSON
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return string
     */
    protected function castAttributeAsJson($key, $value)
    {
        $value = $this->asJson($value);

        if ($value === false) {
            throw JsonEncodingException::forAttribute(
                $this, $key, json_last_error_msg()
            );
        }

        return $value;
    }

    /**
     * Encode the given value as JSON.
	 * 将给定的值编码为JSON
     *
     * @param  mixed  $value
     * @return string
     */
    protected function asJson($value)
    {
        return json_encode($value);
    }

    /**
     * Decode the given JSON back into an array or object.
	 * 将给定的JSON解码回数组或对象
     *
     * @param  string  $value
     * @param  bool  $asObject
     * @return mixed
     */
    public function fromJson($value, $asObject = false)
    {
        return json_decode($value, ! $asObject);
    }

    /**
     * Decode the given float.
	 * 解码给定的浮点数
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function fromFloat($value)
    {
        switch ((string) $value) {
            case 'Infinity':
                return INF;
            case '-Infinity':
                return -INF;
            case 'NaN':
                return NAN;
            default:
                return (float) $value;
        }
    }

    /**
     * Return a decimal as string.
	 * 返回一个小数作为字符串
     *
     * @param  float  $value
     * @param  int  $decimals
     * @return string
     */
    protected function asDecimal($value, $decimals)
    {
        return number_format($value, $decimals, '.', '');
    }

    /**
     * Return a timestamp as DateTime object with time set to 00:00:00.
	 * 返回一个时间戳作为DateTime对象，时间设置为00:00:00。
     *
     * @param  mixed  $value
     * @return \Illuminate\Support\Carbon
     */
    protected function asDate($value)
    {
        return $this->asDateTime($value)->startOfDay();
    }

    /**
     * Return a timestamp as DateTime object.
	 * 返回一个时间戳作为DateTime对象
     *
     * @param  mixed  $value
     * @return \Illuminate\Support\Carbon
     */
    protected function asDateTime($value)
    {
        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to re-instantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
		// 如果此值已经是 Carbon 类型的实例，我们则直接将其原样返回。
		// 这样可以避免我们在已知其为 Carbon 实例的情况下再重新创建一个实例，而这种情况下使用日期时间检查是无法达到这一目的的。
        if ($value instanceof Carbon || $value instanceof CarbonInterface) {
            return Date::instance($value);
        }

        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
		// 如果该值已经是 DateTime 类型的实例，那么我们就不必再进行后续的这些检查了，
		// 因为这些检查会浪费时间，并且会降低字段检查的效率。我们直接返回该 DateTime 即可。
        if ($value instanceof DateTimeInterface) {
            return Date::parse(
                $value->format('Y-m-d H:i:s.u'), $value->getTimezone()
            );
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
		// 如果该值为整数，我们将认为它是UNIX时间戳的值，并根据此时间戳格式化一个Carbon对象。
		// 这使得在定义日期字段时具有一定的灵活性，因为在这里这些字段可能是 UNIX 时间戳形式的。
        if (is_numeric($value)) {
            return Date::createFromTimestamp($value);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
		// 如果该值仅为年、月、日的格式，我们将根据该格式实例化 Carbon 对象。
		// 此外，这使得数据库中的日期字段能够保持简单形式，同时仍支持碳化转换功能。
        if ($this->isStandardDateFormat($value)) {
            return Date::instance(Carbon::createFromFormat('Y-m-d', $value)->startOfDay());
        }

        $format = $this->getDateFormat();

        // https://bugs.php.net/bug.php?id=75577
        if (version_compare(PHP_VERSION, '7.3.0-dev', '<')) {
            $format = str_replace('.v', '.u', $format);
        }

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Carbon object
        // that is returned back out to the developers after we convert it here.
		// 最后，我们将假定这个日期的格式与数据库连接所采用的默认格式相同，并使用该格式来创建一个 Carbon 对象。
		// 在我们在此处进行转换后，该对象会返回给开发人员。
        return Date::createFromFormat($format, $value);
    }

    /**
     * Determine if the given value is a standard date format.
	 * 确定给定的值是否是标准日期格式
     *
     * @param  string  $value
     * @return bool
     */
    protected function isStandardDateFormat($value)
    {
        return preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value);
    }

    /**
     * Convert a DateTime to a storable string.
	 * 将DateTime转换为可存储字符串
     *
     * @param  mixed  $value
     * @return string|null
     */
    public function fromDateTime($value)
    {
        return empty($value) ? $value : $this->asDateTime($value)->format(
            $this->getDateFormat()
        );
    }

    /**
     * Return a timestamp as unix timestamp.
	 * 返回unix时间戳
     *
     * @param  mixed  $value
     * @return int
     */
    protected function asTimestamp($value)
    {
        return $this->asDateTime($value)->getTimestamp();
    }

    /**
     * Prepare a date for array / JSON serialization.
	 * 为数组/ JSON序列化准备一个日期
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->getDateFormat());
    }

    /**
     * Get the attributes that should be converted to dates.
	 * 获取应转换为日期的属性
     *
     * @return array
     */
    public function getDates()
    {
        $defaults = [static::CREATED_AT, static::UPDATED_AT];

        return $this->usesTimestamps()
                    ? array_unique(array_merge($this->dates, $defaults))
                    : $this->dates;
    }

    /**
     * Get the format for database stored dates.
	 * 获取数据库存储日期的格式
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat ?: $this->getConnection()->getQueryGrammar()->getDateFormat();
    }

    /**
     * Set the date format used by the model.
	 * 设置模型使用的日期格式
     *
     * @param  string  $format
     * @return $this
     */
    public function setDateFormat($format)
    {
        $this->dateFormat = $format;

        return $this;
    }

    /**
     * Determine whether an attribute should be cast to a native type.
	 * 确定是否应将属性强制转换为本机类型
     *
     * @param  string  $key
     * @param  array|string|null  $types
     * @return bool
     */
    public function hasCast($key, $types = null)
    {
        if (array_key_exists($key, $this->getCasts())) {
            return $types ? in_array($this->getCastType($key), (array) $types, true) : true;
        }

        return false;
    }

    /**
     * Get the casts array.
	 * 获取强制类型转换数组
     *
     * @return array
     */
    public function getCasts()
    {
        if ($this->getIncrementing()) {
            return array_merge([$this->getKeyName() => $this->getKeyType()], $this->casts);
        }

        return $this->casts;
    }

    /**
     * Determine whether a value is Date / DateTime castable for inbound manipulation.
	 * 确定某个值是否可用于入站操作的Date / DateTime castable
     *
     * @param  string  $key
     * @return bool
     */
    protected function isDateCastable($key)
    {
        return $this->hasCast($key, ['date', 'datetime']);
    }

    /**
     * Determine whether a value is JSON castable for inbound manipulation.
	 * 确定一个值是否可用于入站操作的JSON浇注
     *
     * @param  string  $key
     * @return bool
     */
    protected function isJsonCastable($key)
    {
        return $this->hasCast($key, ['array', 'json', 'object', 'collection']);
    }

    /**
     * Get all of the current attributes on the model.
	 * 获取模型上的所有当前属性
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the array of model attributes. No checking is done.
	 * 设置模型属性数组。没有检查。
     *
     * @param  array  $attributes
     * @param  bool  $sync
     * @return $this
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;

        if ($sync) {
            $this->syncOriginal();
        }

        return $this;
    }

    /**
     * Get the model's original attribute values.
	 * 获取模型的原始属性值
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed|array
     */
    public function getOriginal($key = null, $default = null)
    {
        return Arr::get($this->original, $key, $default);
    }

    /**
     * Get a subset of the model's attributes.
	 * 获取模型属性的子集
     *
     * @param  array|mixed  $attributes
     * @return array
     */
    public function only($attributes)
    {
        $results = [];

        foreach (is_array($attributes) ? $attributes : func_get_args() as $attribute) {
            $results[$attribute] = $this->getAttribute($attribute);
        }

        return $results;
    }

    /**
     * Sync the original attributes with the current.
	 * 将原始属性与当前属性同步
     *
     * @return $this
     */
    public function syncOriginal()
    {
        $this->original = $this->attributes;

        return $this;
    }

    /**
     * Sync a single original attribute with its current value.
	 * 将单个原始属性与其当前值同步
     *
     * @param  string  $attribute
     * @return $this
     */
    public function syncOriginalAttribute($attribute)
    {
        return $this->syncOriginalAttributes($attribute);
    }

    /**
     * Sync multiple original attribute with their current values.
	 * 将多个原始属性与其当前值同步
     *
     * @param  array|string  $attributes
     * @return $this
     */
    public function syncOriginalAttributes($attributes)
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();

        foreach ($attributes as $attribute) {
            $this->original[$attribute] = $this->attributes[$attribute];
        }

        return $this;
    }

    /**
     * Sync the changed attributes.
	 * 同步更改的属性
     *
     * @return $this
     */
    public function syncChanges()
    {
        $this->changes = $this->getDirty();

        return $this;
    }

    /**
     * Determine if the model or any of the given attribute(s) have been modified.
	 * 确定模型或任何给定属性是否已被修改
     *
     * @param  array|string|null  $attributes
     * @return bool
     */
    public function isDirty($attributes = null)
    {
        return $this->hasChanges(
            $this->getDirty(), is_array($attributes) ? $attributes : func_get_args()
        );
    }

    /**
     * Determine if the model and all the given attribute(s) have remained the same.
	 * 确定模型和所有给定属性是否保持不变
     *
     * @param  array|string|null  $attributes
     * @return bool
     */
    public function isClean($attributes = null)
    {
        return ! $this->isDirty(...func_get_args());
    }

    /**
     * Determine if the model or any of the given attribute(s) have been modified.
	 * 确定模型或任何给定属性是否已被修改
     *
     * @param  array|string|null  $attributes
     * @return bool
     */
    public function wasChanged($attributes = null)
    {
        return $this->hasChanges(
            $this->getChanges(), is_array($attributes) ? $attributes : func_get_args()
        );
    }

    /**
     * Determine if any of the given attributes were changed.
	 * 确定是否更改了任何给定属性
     *
     * @param  array  $changes
     * @param  array|string|null  $attributes
     * @return bool
     */
    protected function hasChanges($changes, $attributes = null)
    {
        // If no specific attributes were provided, we will just see if the dirty array
        // already contains any attributes. If it does we will just return that this
        // count is greater than zero. Else, we need to check specific attributes.
		// 如果未提供具体的属性，则我们将检查脏数组中是否已经包含任何属性。
		// 如果情况确实如此，我们就会直接返回“此计数大于零”这一结果。否则，我们需要检查一些特定的属性。
        if (empty($attributes)) {
            return count($changes) > 0;
        }

        // Here we will spin through every attribute and see if this is in the array of
        // dirty attributes. If it is, we will return true and if we make it through
        // all of the attributes for the entire array we will return false at end.
		// 接下来我们将逐一检查每一个属性，看看其中是否有属于“脏属性”的。
		// 如果是这样，我们就返回“true”；而如果我们在遍历完整个数组的所有属性之后仍能继续执行下去，那么我们就返回“false”。
        foreach (Arr::wrap($attributes) as $attribute) {
            if (array_key_exists($attribute, $changes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the attributes that have been changed since last sync.
	 * 获取自上次同步以来已更改的属性
     *
     * @return array
     */
    public function getDirty()
    {
        $dirty = [];

        foreach ($this->getAttributes() as $key => $value) {
            if (! $this->originalIsEquivalent($key, $value)) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Get the attributes that were changed.
	 * 获取已更改的属性
     *
     * @return array
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Determine if the new and old values for a given key are equivalent.
	 * 确定给定键的新旧值是否相等
     *
     * @param  string $key
     * @param  mixed  $current
     * @return bool
     */
    public function originalIsEquivalent($key, $current)
    {
        if (! array_key_exists($key, $this->original)) {
            return false;
        }

        $original = $this->getOriginal($key);

        if ($current === $original) {
            return true;
        } elseif (is_null($current)) {
            return false;
        } elseif ($this->isDateAttribute($key)) {
            return $this->fromDateTime($current) ===
                   $this->fromDateTime($original);
        } elseif ($this->hasCast($key)) {
            return $this->castAttribute($key, $current) ===
                   $this->castAttribute($key, $original);
        }

        return is_numeric($current) && is_numeric($original)
                && strcmp((string) $current, (string) $original) === 0;
    }

    /**
     * Append attributes to query when building a query.
	 * 在构建查询时向查询追加属性
     *
     * @param  array|string  $attributes
     * @return $this
     */
    public function append($attributes)
    {
        $this->appends = array_unique(
            array_merge($this->appends, is_string($attributes) ? func_get_args() : $attributes)
        );

        return $this;
    }

    /**
     * Set the accessors to append to model arrays.
	 * 将访问器设置为追加到模型数组
     *
     * @param  array  $appends
     * @return $this
     */
    public function setAppends(array $appends)
    {
        $this->appends = $appends;

        return $this;
    }

    /**
     * Get the mutated attributes for a given instance.
	 * 获取给定实例的突变属性
     *
     * @return array
     */
    public function getMutatedAttributes()
    {
        $class = static::class;

        if (! isset(static::$mutatorCache[$class])) {
            static::cacheMutatedAttributes($class);
        }

        return static::$mutatorCache[$class];
    }

    /**
     * Extract and cache all the mutated attributes of a class.
	 * 提取并缓存类的所有变异属性
     *
     * @param  string  $class
     * @return void
     */
    public static function cacheMutatedAttributes($class)
    {
        static::$mutatorCache[$class] = collect(static::getMutatorMethods($class))->map(function ($match) {
            return lcfirst(static::$snakeAttributes ? Str::snake($match) : $match);
        })->all();
    }

    /**
     * Get all of the attribute mutator methods.
	 * 获取所有属性变异器方法
     *
     * @param  mixed  $class
     * @return array
     */
    protected static function getMutatorMethods($class)
    {
        preg_match_all('/(?<=^|;)get([^;]+?)Attribute(;|$)/', implode(';', get_class_methods($class)), $matches);

        return $matches[1];
    }
}
