<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 11.12.2019
 * Time 8:38
 */

namespace prokhonenkov\xhtmlparser\classes\query;


use prokhonenkov\xhtmlparser\classes\models\Attribute;
use prokhonenkov\xhtmlparser\classes\models\Text;
use prokhonenkov\xhtmlparser\classes\search\SearchItem;

/**
 * Class QueryBuilder
 * @package prokhonenkov\xhtmlparser\classes
 */
class QueryBuilder
{
	/**
	 * @var string
	 */
	private $tagName;
	/**
	 * @var array
	 */
	private $attributes = [];
	/**
	 * @var array
	 */
	private $texts = [];

	/**
	 * QueryBuilder constructor.
	 * @param string $tagName
	 */
	public function __construct(string $tagName = '.')
	{
		$this->tagName = $tagName;
	}

	/**
	 * @param Attribute ...$attributes
	 * @return $this
	 */
	public function setAttributes(Attribute ...$attributes): self
	{
		$this->attributes = $attributes;

		return $this;
	}

	/**
	 * @param Text ...$texts
	 * @return $this
	 */
	public function setTexts(Text ...$texts): self
	{
		$this->texts = $texts;

		return $this;
	}

	/**
	 * @return string
	 */
	public function build(): string
	{
		return $this->tagName === '.'
			? $this->insideTagQuery()
			: $this->outsideTagQuery();
	}

	/**
	 * @return string
	 */
	private function buildTagCondition(): string
	{
		return './/' . $this->tagName;
	}

	/**
	 * @return array
	 */
	private function buildAttributeConditions(): array
	{
		return array_map(function(Attribute $attribute) {
			if($attribute->getValue() === SearchItem::DEFAULT_ATTRIBUTE_VALUE) {
				return "@{$attribute->getName()}";
			}
			return "contains(@{$attribute->getName()}, '{$attribute->getValue()}')";
		}, $this->attributes);
	}

	/**
	 * @return array
	 */
	private function buildTextConditions(): array
	{
		return array_map(function(Text $text) {
			$text = str_replace("'", '', $text->getValue());
			return "contains(text(), '$text')";
		}, $this->texts);
	}

	/**
	 * @return string
	 */
	private function insideTagQuery(): string
	{
		$query = [];

		/** @var Attribute $attribute */
		foreach ($this->attributes as $attribute) {
			$query[] = "@{$attribute->getName()}[contains(., '{$attribute->getValue()}')]";
		}

		/** @var Text $text */
		foreach ($this->texts as $text) {
			$query[] = "//text()[contains(.,'{$text->getValue()}')]";
		}

		return implode(' | ', $query);
	}

	/**
	 * @return string
	 */
	private function outsideTagQuery(): string
	{
		$tagCondition = $this->buildTagCondition();
		$attributeConditions = $this->buildAttributeConditions();
		$textConditions = $this->buildTextConditions();

		if($attributeConditions || $textConditions) {
			$tagCondition .= '[' . implode(' and ', array_merge($attributeConditions, $textConditions)) . ']';
		}

		return $tagCondition;
	}
}