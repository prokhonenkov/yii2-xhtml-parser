<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 13.12.2019
 * Time 8:59
 */

namespace prokhonenkov\xhtmlparser\classes\models;

/**
 * Class Text
 * @package prokhonenkov\xhtmlparser\classes\models
 */
class Text
{
	/**
	 * @var string
	 */
	private $value;

	/**
	 * Text constructor.
	 * @param string $value
	 */
	public function __construct(string $value)
	{
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getValue(): string
	{
		return $this->value;
	}
}