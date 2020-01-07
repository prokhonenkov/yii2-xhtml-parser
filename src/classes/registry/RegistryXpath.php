<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 23.12.2019
 * Time 17:59
 */

namespace prokhonenkov\xhtmlparser\classes\registry;


class RegistryXpath
{
	private static $instance = null;
	private $xpath;

	private function __clone() {}
	private function __construct() {}

	public static function getInstance(): self
	{
		if(self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @param DOMXPath $xpath
	 * @return $this
	 */
	public function set(\DOMXPath $xpath): self
	{
		$this->xpath = $xpath;
		return $this;
	}

	/**
	 * @param string $key
	 * @return DOMXPath
	 */
	public function get(): \DOMXPath
	{
		return $this->xpath;
	}
}