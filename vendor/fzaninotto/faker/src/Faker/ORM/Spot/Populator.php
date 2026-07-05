<?php
/**
 * Faker，ORM，Spot，普及者
 */

namespace Faker\ORM\Spot;

use Spot\Locator;

/**
 * Service class for populating a database using the Spot ORM.
 * 使用点ORM填充数据库的服务类。
 */
class Populator
{
    protected $generator;
    protected $locator;
    protected $entities = array();
    protected $quantities = array();

    /**
     * Populator constructor.
	 * 填充构造函数
     * @param \Faker\Generator $generator
     * @param Locator|null $locator
     */
    public function __construct(\Faker\Generator $generator, Locator $locator = null)
    {
        $this->generator = $generator;
        $this->locator = $locator;
    }

    /**
     * Add an order for the generation of $number records for $entity.
	 * 为$实体的生成值的生成添加一个订单
     *
     * @param $entityName string Name of Entity object to generate
     * @param $number int The number of entities to populate
     * @param $customColumnFormatters array
     * @param $customModifiers array
     * @param $useExistingData bool Should we use existing rows (e.g. roles) to populate relations?
     */
    public function addEntity(
        $entityName,
        $number,
        $customColumnFormatters = array(),
        $customModifiers = array(),
        $useExistingData = false
    ) {
        $mapper = $this->locator->mapper($entityName);
        if (null === $mapper) {
            throw new \InvalidArgumentException("No mapper can be found for entity " . $entityName);
        }
        $entity = new EntityPopulator($mapper, $this->locator, $useExistingData);

        $entity->setColumnFormatters($entity->guessColumnFormatters($this->generator));
        if ($customColumnFormatters) {
            $entity->mergeColumnFormattersWith($customColumnFormatters);
        }
        $entity->mergeModifiersWith($customModifiers);

        $this->entities[$entityName] = $entity;
        $this->quantities[$entityName] = $number;
    }

    /**
     * Populate the database using all the Entity classes previously added.
	 * 使用之前添加的所有实体类填充数据库
     *
     * @param Locator $locator A Spot locator
     *
     * @return array A list of the inserted PKs
     */
    public function execute($locator = null)
    {
        if (null === $locator) {
            $locator = $this->locator;
        }
        if (null === $locator) {
            throw new \InvalidArgumentException("No entity manager passed to Spot Populator.");
        }

        $insertedEntities = array();
        foreach ($this->quantities as $entityName => $number) {
            for ($i = 0; $i < $number; $i++) {
                $insertedEntities[$entityName][] = $this->entities[$entityName]->execute(
                    $insertedEntities
                );
            }
        }

        return $insertedEntities;
    }
}
