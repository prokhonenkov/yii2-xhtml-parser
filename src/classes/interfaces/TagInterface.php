<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 08.12.2019
 * Time 14:44
 */

namespace prokhonenkov\xhtmlparser\classes\interfaces;

/**
 * Interface TagInterface
 * @package prokhonenkov\xhtmlparser\classes\interfaces
 */
interface TagInterface
{
	/**
	 * @return string
	 */
	public function getName(): string ;

	/**
	 * @return string
	 */
	public function getText(): string ;

	/**
	 * @param string $name
	 * @return string|null
	 */
	public function getAttribute(string $name): ?string ;

	/**
	 * @return \DOMElement|null
	 */
	public function getDomElement(): ?\DOMElement ;

	/**
	 * @return QueryInterface
	 */
	public function find() : QueryInterface;
}