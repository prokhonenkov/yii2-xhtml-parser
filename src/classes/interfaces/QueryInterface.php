<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 08.12.2019
 * Time 14:38
 */

namespace prokhonenkov\xhtmlparser\classes\interfaces;

use prokhonenkov\xhtmlparser\classes\search\SearchItem;

/**
 * Interface QueryInterface
 * @package prokhonenkov\xhtmlparser\classes
 */
interface QueryInterface
{
	/**
	 * @param string $tag
	 * @return $this
	 */
	public function child(string $tag = SearchItem::DEFAULT_TAGNAME): self ;

	/**
	 * @param string $tag
	 * @return $this
	 */
	public function parent(string $tag = SearchItem::DEFAULT_TAGNAME): self ;

	/**
	 * @param string $name
	 * @param string $value
	 * @return $this
	 */
	public function attribute(string $name, string $value = SearchItem::DEFAULT_ATTRIBUTE_VALUE): self ;

	/**
	 * @param string $text
	 * @return $this
	 */
	public function text(string $text): self ;

	/**
	 * @param string $name
	 * @return $this
	 */
	public function alias(string $name): self ;

	/**
	 * @return $this
	 */
	public function begin(): self ;

	/**
	 * @return $this
	 */
	public function end(): self ;

	/**
	 * @return array|SearchResultsTree
	 */
	public function execute() ;

	/**
	 * @param \DOMElement $context
	 * @return $this
	 */
	public function setContext(\DOMElement $context): self;

	/**
	 * @return \DOMElement|null
	 */
	public function getContext(): ?\DOMElement ;
}