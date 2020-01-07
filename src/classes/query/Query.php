<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 07.12.2019
 * Time 11:24
 */

namespace prokhonenkov\xhtmlparser\classes\query;


use prokhonenkov\xhtmlparser\classes\interfaces\QueryInterface;
use prokhonenkov\xhtmlparser\classes\models\Tag;
use prokhonenkov\xhtmlparser\classes\search\Finder;
use prokhonenkov\xhtmlparser\classes\search\SearchItem;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidArgumentException;


/**
 * Class Query
 * @package prokhonenkov\xhtmlparser\classes
 */
class Query extends Component implements QueryInterface
{
	/**
	 * @var array
	 */
	private $searchItemTree = [];
	/**
	 * @var \DOMElement|null
	 */
	private $context;
	/**
	 * @var null|string
	 */
	private $parentKey;
	/**
	 * @var null|string
	 */
	private $currentKey;
	/**
	 * @var array
	 */
	private $keyCollection = [];
	/**
	 * @var null|SearchItem
	 */
	private $currentSearchItem;

	/**
	 * Query constructor.
	 * @param \DOMElement|null $context
	 * @param array $config
	 */
	public function __construct(?\DOMElement $context = null, $config = [])
	{
		parent::__construct($config);

		$this->context = $context;
	}

	/**
	 * @return \DOMElement|null
	 */
	public function getContext(): ?\DOMElement
	{
		return $this->context;
	}

	/**
	 * @param string $tag
	 * @param string $type
	 * @return QueryInterface
	 */
	private function tag(string $tag, string $type): QueryInterface
	{
		$this->setCurrentKey();

		$this->pushTag(
			$tag,
			$type,
			$this->searchItemTree
		);
		return $this;
	}

	/**
	 * @param string $tag
	 * @return QueryInterface
	 */
	public function child(string $tag = SearchItem::DEFAULT_TAGNAME): QueryInterface
	{
		return $this->tag($tag, SearchItem::TYPE_CHILD);
	}

	/**
	 * @param string $tag
	 * @return QueryInterface
	 */
	public function parent(string $tag = SearchItem::DEFAULT_TAGNAME): QueryInterface
	{
		return $this->tag($tag, SearchItem::TYPE_PARENT);
	}

	/**
	 * @return QueryInterface
	 */
	public function begin(): QueryInterface
	{
		if(!$this->currentKey) {
			throw new InvalidArgumentException('The query must begin with a root tag definition.');
		}

		$this->setParentKey($this->currentKey);
		$this->addCurrentKeyToCollection();
		return $this;
	}

	/**
	 * @return QueryInterface
	 */
	public function end(): QueryInterface
	{
		$this->removeLastkeyFromCollection();

		if(count($this->keyCollection)) {
			$this->setParentKey(
				$this->getLastKeyCollection()
			);
		} else {
			$this->unSetParentKey();
		}

		return $this;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return QueryInterface
	 */
	public function attribute(string $name, string $value = SearchItem::DEFAULT_ATTRIBUTE_VALUE): QueryInterface
	{
		$this->currentSearchItem->setAttribute($name, $value);
		return $this;
	}


	/**
	 * @param string $text
	 * @return QueryInterface
	 */
	public function text(string $text): QueryInterface
	{
		$this->currentSearchItem->setText($text);
		return $this;
	}

	/**
	 * @param string $name
	 * @return QueryInterface
	 */
	public function alias(string $name): QueryInterface
	{
		$this->currentSearchItem->setAlias(lcfirst($name));

		return $this;
	}

	/**
	 * @return Tag|null
	 * @throws Exception
	 */
	public function execute(): ?Tag
	{
		$finder = new Finder($this);

		if(count($this->searchItemTree) > 1) {
			throw new Exception('The tree must have one root element.');
		}

		$result = $finder->search(array_shift($this->searchItemTree));

		if(empty($result)) {
			return null;
		}

		return new Tag($result);
	}

	/**
	 * @param \DOMElement $context
	 * @return QueryInterface
	 */
	public function setContext(\DOMElement $context): QueryInterface
	{
		$this->context = $context;
		return $this;
	}

	/**
	 * @param string $tag
	 * @param string $type
	 * @param array $tree
	 */
	private function pushTag(string $tag, string $type, array &$tree)
	{
		$this->currentSearchItem = new SearchItem($tag, $type);

		if(!$this->parentKey) {
			$tree[$this->currentKey] = [$this->currentSearchItem];
		} elseif(isset($tree[$this->parentKey])) {
			$tree[$this->parentKey] = array_merge($tree[$this->parentKey], [$this->currentKey => [$this->currentSearchItem]]);
		} else {
			foreach ($tree as $key => $item) {
				if(!is_array($item)) {
					continue;
				}
				$this->pushTag($tag, $type, $item);
				$tree[$key] = $item;
			}
		}
	}

	/**
	 *
	 */
	private function setCurrentKey(): void
	{
		$this->currentKey = md5(microtime());
	}

	/**
	 * @param string $key
	 */
	private function setParentKey(string $key): void
	{
		$this->parentKey = $key;
	}

	/**
	 *
	 */
	private function unSetParentKey(): void
	{
		$this->parentKey = null;
	}

	/**
	 *
	 */
	private function addCurrentKeyToCollection()
	{
		$this->keyCollection[] = $this->currentKey;
	}

	/**
	 *
	 */
	private function removeLastkeyFromCollection(): void
	{
		array_pop($this->keyCollection);
	}

	/**
	 * @return string
	 */
	private function getLastKeyCollection(): string
	{
		return $this->keyCollection[count($this->keyCollection)-1];
	}
}