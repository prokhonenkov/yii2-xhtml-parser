<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 13.12.2019
 * Time 8:40
 */

namespace prokhonenkov\xhtmlparser\classes\search;

use prokhonenkov\xhtmlparser\classes\models\Attribute;
use prokhonenkov\xhtmlparser\classes\models\Text;

/**
 * Class SearchItem
 * @package prokhonenkov\xhtmlparser\classes\models
 */
class SearchItem
{
	const TYPE_CHILD = 'child';
	const TYPE_PARENT = 'parent';
	const DEFAULT_TAGNAME = '*';
	const DEFAULT_ATTRIBUTE_VALUE = '*';

	/**
	 * @var string
	 */
	private $tagName;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var array
	 */
	private $attributes = [];
	/**
	 * @var array
	 */
	private $texts = [];
	/**
	 * @var string
	 */
	private $alias;

	/**
	 * SearchItem constructor.
	 * @param string $tagName
	 * @param string $type
	 */
	public function __construct(string $tagName, string $type)
	{
		$this->tagName = $tagName;
		$this->type = $type;
	}

	/**
	 * @param string $alias
	 * @return $this
	 */
	public function setAlias(string $alias): self
	{
		$this->alias = $alias;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getAlias(): string
	{
		return $this->alias??$this->tagName;
	}

	/**
	 * @return string
	 */
	public function getTagName(): string
	{
		return $this->tagName;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return array
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return $this
	 */
	public function setAttribute(string $name, string $value): self
	{
		$this->attributes[] = new Attribute($name, $value);

		return $this;
	}

	/**
	 * @return array
	 */
	public function getTexts(): array
	{
		return $this->texts;
	}

	/**
	 * @param string $text
	 * @return $this
	 */
	public function setText(string $text): self
	{
		$this->texts[] = new Text($text);

		return $this;
	}

	/**
	 * @param string $tagName
	 * @return bool
	 */
	public function compareTag(string $tagName): bool
	{
		if($this->tagName === self::DEFAULT_TAGNAME) {
			return true;
		}

		return $this->tagName === $tagName;
	}
}