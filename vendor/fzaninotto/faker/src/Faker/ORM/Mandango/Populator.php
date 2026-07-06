<?php
/**
 * Faker，ORM，Mandango，Populator
 */

namespace Faker\ORM\Mandango;

use Mandango\Mandango;

/**
 * Service class for populating a database using Mandango.
 * A Populator can populate several tables using ActiveRecord classes.
 * 使用Mandango填充数据库的服务类。
 */
class Populator
{
    protected $generator;
    protected $mandango;
    protected $entities = array();
    protected $quantities = array();

    /**
     * @param \Faker\Generator $generator
     * @param Mandango $mandango
     */
    public function __construct(\Faker\Generator $generator, Mandango $mandango)
    {
        $this->generator = $generator;
        $this->mandango = $mandango;
    }

    /**
     * Add an order for the generation of $number records for $entity.
	 * 为$entity的生成值的生成添加一个订单
     *
     * @param mixed $entity A Propel ActiveRecord classname, or a \Faker\ORM\Propel\EntityPopulator instance
     * @param int   $number The number of entities to populate
     */
    public function addEntity($entity, $number, $customColumnFormatters = array())
    {
        if (!$entity instanceof \Faker\ORM\Mandango\EntityPopulator) {
            $entity = new \Faker\ORM\Mandango\EntityPopulator($entity);
        }
        $entity->setColumnFormatters($entity->guessColumnFormatters($this->generator, $this->mandango));
        if ($customColumnFormatters) {
            $entity->mergeColumnFormattersWith($customColumnFormatters);
        }
        $class = $entity->getClass();
        $this->entities[$class] = $entity;
        $this->quantities[$class] = $number;
    }

    /**
     * Populate the database using all the Entity classes previously added.
     *
     * @return array A list of the inserted entities.
     */
    public function execute()
    {
        $insertedEntities = array();
        foreach ($this->quantities as $class => $number) {
            for ($i=0; $i < $number; $i++) {
                $insertedEntities[$class][]= $this->entities[$class]->execute($this->mandango, $insertedEntities);
            }
        }
        $this->mandango->flush();

        return $insertedEntities;
    }
}
