<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 07.12.2019
 * Time 8:57
 */

namespace prokhonenkov\xhtmlparser;

use prokhonenkov\xhtmlparser\classes\interfaces\QueryInterface;
use prokhonenkov\xhtmlparser\classes\interfaces\XHtmlParserInterface;
use prokhonenkov\xhtmlparser\classes\query\Query;
use prokhonenkov\xhtmlparser\classes\registry\RegistryXpath;
use yii\base\Component;

/**
 * Class XHtmlParser
 * @package prokhonenkov\xhtmlparser
 */
class XHtmlParser extends Component implements XHtmlParserInterface
{
	const DRIVER_XML = 'xml';
	const DRIVER_HTML = 'html';

	/**
	 * @var \DOMXPath
	 */
	private $xpath;

	/**
	 * @param string $content
	 * @param string|null $driver
	 * @return XHtmlParserInterface
	 */
	public function parse(string $content, string $driver = null): XHtmlParserInterface
	{
		if(!$driver) {
			$driver = self::DRIVER_HTML;
		}

		$this->xpath = $this->load(
			$this->clear($content),
			$driver
		);

		RegistryXpath::getInstance()->set($this->xpath);

		return $this;
	}

	/**
	 * @param string $response
	 * @return string
	 */
	private function clear(string $response): string
	{
		return preg_replace("/\n|\r\n/", '', $response);
	}

	/**
	 * @param string $content
	 * @param string $driver
	 * @return \DOMXPath
	 */
	private function load(string $content, string $driver): \DOMXPath
	{
		$dom = new \DOMDocument('1.0', 'utf-8');
		libxml_use_internal_errors(true);

		if($driver === self::DRIVER_XML) {
			$dom->loadxml( $content );
		} else {
			$dom->loadHTML($content);
		}

		libxml_clear_errors();

		return new \DOMXPath( $dom );
	}

	/**
	 * @return QueryInterface
	 */
	public function find(): QueryInterface
	{
		return new Query();
	}

	public function getXpath(): \DOMXPath
	{
		return $this->xpath;
	}
}