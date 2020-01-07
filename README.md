XHtml parser
==============
This extension parses an HTML page or XML document and returns a tree of the result. 

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require prokhonenkov/yii2-xhtml-parser
```

or add

```
"prokhonenkov/yii2-xhtml-parser": "*"
```

to the require section of your `composer.json` file.

Configuration
-------------

Add component declaration to your config file for web config:
```php
<?php

return [
    // ... your config
    'components' => [
        'xHtmlParser' => [
            'class' => \prokhonenkov\xhtmlparser\XHtmlParser::class
        ],
    ]
];

```

Usage
-----
Example:
```php

//Pass HTML content to the parser 
$result = \Yii::$app->xHtmlParser->parse($html);
//or pass xml content to the parser 
$result = \Yii::$app->xHtmlParser->parse($xml , prokhonenkov\xhtmlparser\XHtmlParser::DRIVER_XML);

/** Build query  */

/** @var prokhonenkov\xhtmlparser\classes\interfaces\QueryInterface $query */
$query = $result->find(); 

/** @var \prokhonenkov\xhtmlparser\classes\interfaces\TagInterface $treeOfResults */
$treeOfResults = $query
    ->child('div')->attribute('class', 'movie-info-wrapper')->alias('container') // search by tag name and attribute value
    ->begin() //search inside the previous tag (inside the "div")
        ->child('img')->attribute('width', '1190')->alias('mainImage') // search by tag name and attribute value
        ->child('div')->attribute('id', 'left_column')->alias('mainDiv') // search by tag name and attribute value
        ->begin() //search inside the previous tag (inside the "div")
            ->child('td')->text('Some text')->alias('production')  // search by tag name and some text contained in the "td" tag
            ->begin() //search inside the previous tag (inside the "td")
                ->parent('tr') //search parrent tag by name
                ->begin() //search inside the previous tag (inside the "tr")
                    ->child('td') //Get child tags "td"
                ->end()
            ->end()
        ->end()
        ->child('th')->text('Acters')->alias('acters') // search by tag name and some text contained in the "th" tag
        ->begin()
            ->parent('table') // Get parrent node
            ->begin()
                ->child('td') // Get child nodes
            ->end()
        ->end()
    ->end()
->execute();

/** Getting results of search */

//Get search result by tag alias. It returns \SplFixedArray instance.
$containerList = $treeOfResults->getContainer();

/** @var \prokhonenkov\xhtmlparser\classes\interfaces\TagInterface $container */
$container = $containerList->current(); 

/** @var \prokhonenkov\xhtmlparser\classes\interfaces\TagInterface $mainDiv */
$mainDiv = $container
    ->getMainDiv() // returns \SplFixedArray
    ->current(); 

/** @var string $mainImage */
$mainImage = $container->getMainImage()->current()->getAttribute('src'); // Get attribute value

/** @var string $production */
$production = $mainDiv
    ->getProduction()->current() // Get tag td by alias "production"
    ->getTr()->current() // Get tag "tr" by name
    ->getTd()->offsetGet(1) //Get second tag "td"
    ->getText(); // Get text content   
```  

Also, you may search into current context.

Example:
```php

/** @var \prokhonenkov\xhtmlparser\classes\interfaces\TagInterface $something */
$something = $mainDiv->find()
    ->child('div')->attribute('data-attribute') // Search by tag name which has attribute "data-attribute"
    ->begin()
       ->child('span')->attribute('class', 'someclass')->attribute('data-num')->alias('spanAlias')
    ->end()
    ->execute();

/** @var \prokhonenkov\xhtmlparser\classes\interfaces\TagInterface $div */
$div = $something->getDiv();
$texts = [];
if($div->count()) {
    /** @var SplFixedArray $spanAlias */
    $spanAlias = $div->current()->getSpanAlias();
    /** @var \prokhonenkov\xhtmlparser\classes\interfaces\TagInterface $item */
    foreach($spanAlias as $item) {
        $texts[$item->getAttribute('data-num')] = $item->getText();
    }
}

```  

If you need to do a more complex search, you can use xPath.

Example:
```php
/** @var \DOMXPath $xPath */
$xPath = $result->getXpath();

/** @var \DOMElement $domElement */
$domElement = $mainDiv->getDomElement(); 

// Search into context $domElement
$xPath->query('xPath query', $domElement);

```  