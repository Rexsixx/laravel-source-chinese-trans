<?php
/**
 * Illuminate，基础，测试，与 Faker
 */

namespace Illuminate\Foundation\Testing;

use Faker\Factory;

trait WithFaker
{
    /**
     * The Faker instance.
	 * Faker实例
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Setup up the Faker instance.
	 * 设置Faker实例
     *
     * @return void
     */
    protected function setUpFaker()
    {
        $this->faker = $this->makeFaker();
    }

    /**
     * Get the default Faker instance for a given locale.
	 * 获取给定语言环境的默认Faker实例
     *
     * @param  string  $locale
     * @return \Faker\Generator
     */
    protected function faker($locale = null)
    {
        return is_null($locale) ? $this->faker : $this->makeFaker($locale);
    }

    /**
     * Create a Faker instance for the given locale.
	 * 为给定的语言环境创建一个Faker实例
     *
     * @param  string  $locale
     * @return \Faker\Generator
     */
    protected function makeFaker($locale = null)
    {
        return Factory::create($locale ?? Factory::DEFAULT_LOCALE);
    }
}
