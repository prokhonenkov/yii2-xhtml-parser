<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 13.12.2019
 * Time 8:52
 */

namespace prokhonenkov\xhtmlparser\classes\models;

/**
 * Class Attribute
 * @package prokhonenkov\xhtmlparser\classes\models
 */
class Attribute
{
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $value;

	/**
	 * Attribute constructor.
	 * @param string $name
	 * @param string $value
	 */
	public function __construct(string $name, string $value)
	{
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getValue(): string
	{
		return $this->value;
	}
}