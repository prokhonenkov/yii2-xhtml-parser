<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 13.12.2019
 * Time 8:30
 */

namespace prokhonenkov\xhtmlparser\classes\search;


use prokhonenkov\xhtmlparser\classes\query\Query;
use prokhonenkov\xhtmlparser\classes\query\QueryBuilder;
use prokhonenkov\xhtmlparser\classes\registry\RegistryXpath;
use yii\helpers\VarDumper;

/**
 * Class Finder
 * @package prokhonenkov\xhtmlparser\classes
 */
class Finder
{
	/**
	 * @var Query
	 */
	private $query;
	/**
	 * @var \DOMXPath
	 */
	private $xPath;

	/**
	 * Finder constructor.
	 * @param Query $query
	 */
	public function __construct(Query $query)
	{
		$this->query = $query;
		$this->xPath = RegistryXpath::getInstance()->get();
	}

	/**
	 * @param array $searchItemTree
	 * @return SearchResultsTree
	 */
	public function search(array $searchItemTree)
	{
		$result = new SearchResultsTree(clone($this->query));
		$this->applySearch($searchItemTree, $result);
//VarDumper::dump($result, 10, true);exit;
		return $result;
	}

	/**
	 * @param SearchItem $searchItem
	 * @param \DOMElement $node
	 * @return \SplFixedArray
	 */
	private function parentNodeSearch(SearchItem $searchItem, \DOMElement $node): \SplFixedArray
	{
		if(!isset($node->parentNode->tagName)) {
			return new \SplFixedArray();
		}

		$selfReturn = function() use($searchItem,  $node): \SplFixedArray {
			return $this->parentNodeSearch($searchItem, $node->parentNode);
		};

		if(!$searchItem->compareTag($node->parentNode->tagName)) {
			return $selfReturn();
		}

		if(!empty($searchItem->getAttributes())) {
			$result = $this->xPath->query(
				(new QueryBuilder())
					->setAttributes(...$searchItem->getAttributes())
					->setTexts(...$searchItem->getTexts())
					->build(),
				$node->parentNode
			);

			if(!$result->length || (int)$result->length !== count($searchItem->getAttributes()) ) {
				return $selfReturn();
			}
		}

		$return = new \SplFixedArray(1);
		$return->offsetSet(0, $node->parentNode);

		return $return;
	}

	/**
	 * @param SearchItem $searchItem
	 * @param \DOMElement|null $context
	 * @return \SplFixedArray
	 */
	private function childNodeSearch(SearchItem $searchItem, ?\DOMElement $context = null): \SplFixedArray
	{
		$query = (new QueryBuilder($searchItem->getTagName()))
			->setAttributes(...$searchItem->getAttributes())
			->setTexts(...$searchItem->getTexts())
			->build();

		$result = $this->xPath->query($query, $context);

		if(empty($result) || (int)$result->length === 0) {
			return new \SplFixedArray();
		}

		$elements = new \SplFixedArray($result->count());

		for ($i=0;$i<$result->length;$i++) {
			$elements[$i] = $result->item($i);
		}

		return $elements;
	}

	/**
	 * @param array $searchItemTree
	 * @param SearchResultsTree $searchResultsTree
	 * @return bool
	 */
	private function applySearch(array $searchItemTree, SearchResultsTree &$searchResultsTree)
	{
		/** @var SearchItem $mainSearchItem */
		$mainSearchItem = array_shift($searchItemTree);

		/** @var \SplFixedArray $result */
		$result = $this->{$mainSearchItem->getType() . 'NodeSearch'}($mainSearchItem, $searchResultsTree->getQuery()->getContext());

		if(!$searchItemTree) {
			$this->fillChildren($mainSearchItem, $searchResultsTree, $result);
		} else {
			$this->fillChildren($mainSearchItem, $searchResultsTree, $result, function ($tag) use($searchItemTree) {
				foreach ($searchItemTree as $key => $searchItem) {
					$this->applySearch($searchItem, $tag);
				}
			});
		}

		return true;
	}

	/**
	 * @param SearchItem $mainSearchItem
	 * @param SearchResultsTree $searchResultsTree
	 * @param \SplFixedArray $elements
	 * @param \Closure|null $func
	 */
	private function fillChildren(SearchItem $mainSearchItem,  SearchResultsTree $searchResultsTree, \SplFixedArray $elements, \Closure $func = null)
	{
		if(!$elements->count()) {
			$searchResultsTree->setChild(null, $mainSearchItem->getAlias());
		} else {
			foreach ($elements as $context) {
				$tag = new SearchResultsTree(
					(clone $this->query)->setContext($context)
				);
				if($func) {
					$func($tag);
				}
				$searchResultsTree->setChild($tag, $mainSearchItem->getAlias());
			}
		}
	}
}