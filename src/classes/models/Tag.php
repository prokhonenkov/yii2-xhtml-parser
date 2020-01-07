<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 25.12.2019
 * Time 18:43
 */

namespace prokhonenkov\xhtmlparser\classes\models;


use prokhonenkov\xhtmlparser\classes\interfaces\QueryInterface;
use prokhonenkov\xhtmlparser\classes\interfaces\TagInterface;
use prokhonenkov\xhtmlparser\classes\search\SearchResultsTree;
use yii\base\UnknownPropertyException;

class Tag implements TagInterface
{
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var SearchResultsTree
	 */
	private $tagsTree;
	/**
	 * @var array
	 */
	private $attributes = [];

	/**
	 * Tag constructor.
	 * @param SearchResultsTree $tagsTree
	 */
	public function __construct(SearchResultsTree $tagsTree)
	{
		$this->tagsTree = $tagsTree;

		if($this->tagsTree->getQuery()->getContext() && $this->tagsTree->getQuery()->getContext()->attributes->length) {
			for($i=0;$i<$this->tagsTree->getQuery()->getContext()->attributes->length;$i++) {
				$attribute = $this->tagsTree->getQuery()->getContext()->attributes->item($i);
				$this->attributes[$attribute->name] = $attribute->value;
			}
		}
	}

	/**
	 * @param string $name
	 * @param $arguments
	 * @return \SplFixedArray
	 */
	public function __call(string $name, $arguments): \SplFixedArray
	{
		$index = preg_replace('/^get/', '', $name);

		$trees = $this->tagsTree->getChild(lcfirst($index));

		if(is_null($trees)) {
			throw new UnknownPropertyException(sprintf('Unknown tag alias: %s',
				$index
			));
		}

		$this->alias = $index;

		return $this->getTagCollection(...$trees);
	}

	/**
	 * @param SearchResultsTree ...$trees
	 * @return \SplFixedArray
	 */
	private function getTagCollection(SearchResultsTree ...$trees): \SplFixedArray
	{
		$tags = new \SplFixedArray(count($trees));

		foreach ($trees as $k => $tree) {
			$tags[$k] = new self($tree);
		}

		return $tags;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		if(!$this->tagsTree->getQuery()->getContext()) {
			return 'root';
		}
		return $this->tagsTree->getQuery()->getContext()->tagName;
	}

	/**
	 * @return string
	 */
	public function getText(): string
	{
		if(!$this->tagsTree->getQuery()->getContext()) {
			return '';
		}
		return trim($this->tagsTree->getQuery()->getContext()->textContent);
	}

	/**
	 * @param string $name
	 * @return string|null
	 */
	public function getAttribute(string $name): ?string
	{
		if(!isset($this->attributes[$name])) {
			return null;
		}

		return $this->attributes[$name];
	}

	/**
	 * @return \DOMElement|null
	 */
	public function getDomElement(): ?\DOMElement
	{
		return $this->tagsTree->getQuery()->getContext();
	}

	/**
	 * @return QueryInterface
	 */
	public function find(): QueryInterface
	{
		return $this->tagsTree->getQuery();
	}
}