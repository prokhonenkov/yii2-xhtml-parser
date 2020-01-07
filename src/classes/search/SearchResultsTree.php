<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 20.12.2019
 * Time 18:03
 */

namespace prokhonenkov\xhtmlparser\classes\search;


use prokhonenkov\xhtmlparser\classes\interfaces\QueryInterface;

/**
 * Class SearchResultsTree
 * @package prokhonenkov\xhtmlparser\classes
 */
class SearchResultsTree
{
	/**
	 * @var array
	 */
	private $children = [];
	/**
	 * @var QueryInterface
	 */
	private $query;

	/**
	 * SearchResultsTree constructor.
	 * @param QueryInterface $query
	 */
	public function __construct(QueryInterface $query)
	{
		$this->query = $query;
	}

	/**
	 * @param SearchResultsTree|null $child
	 * @param string $allias
	 */
	public function setChild(?self $child, string $allias): void
	{
		if(is_null($child)) {
			$this->children[$allias] = [];
		} else {
			$this->children[$allias][] = $child;
		}
	}

	/**
	 * @param string $name
	 * @return array|null
	 */
	public function getChild(string $name): ?array
	{
		return $this->children[$name]??null;
	}

	/**
	 * @return QueryInterface
	 */
	public function getQuery(): QueryInterface
	{
		return $this->query;
	}

	public function getCountChildren($name)
	{
		return count($this->children);
	}
}