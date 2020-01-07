<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 18.12.2019
 * Time 18:35
 */

namespace prokhonenkov\xhtmlparser\classes\interfaces;


/**
 * Interface XHtmlParserInterface
 * @package prokhonenkov\xhtmlparser\classes\interfaces
 */
interface XHtmlParserInterface
{
	/**
	 * @param string $content
	 * @param string|null $driver
	 * @return $this
	 */
	public function parse(string $content, string $driver = null): self ;

	/**
	 * @return QueryInterface
	 */
	public function find(): QueryInterface ;

	/**
	 * @return \DOMXPath
	 */
	public function getXpath(): \DOMXPath ;
}