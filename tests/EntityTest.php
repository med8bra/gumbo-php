<?php
/**
 * Copyright 2016 Alexander Fedyashov
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Layershifter\Gumbo\Tests;

use Layershifter\Gumbo\Parser;

class EntityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test entity encoding.
     *
     * @param string $html
     * @param string $tagName
     * @param string $text
     *
     * @dataProvider entitiesProvider
     *
     * @return void
     */
    public function testEntities($html, $tagName, $text)
    {
        $document = Parser::load($html);

        static::assertEquals(1, $document->childNodes->length);

        $node = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $node);
        static::assertEquals($tagName, $node->nodeName);
        static::assertEquals($text, $node->textContent);
        static::assertEquals(1, $node->childNodes->length);
        static::assertEquals(0, $node->attributes->length);
    }

    /**
     * Provides test snippets for testEntities().
     *
     * @return array
     */
    public function entitiesProvider()
    {
        return [
            [
                '<a>Apples &amp; bananas.</a>',
                'a',
                'Apples &amp; bananas.'
            ],
            [
                '<p>R&D</p>',
                'p',
                'R&D'
            ],
            [
                '<p>' .
                'An R&D string with one more R&amp;D here and S&D or T&engrave; Some More T&D and &amp;&engrave; ' .
                'and R&&D' .
                '</p>',
                'p',
                'An R&D string with one more R&amp;D here and S&D or T&engrave; Some More T&D and &amp;&engrave; ' .
                'and R&&D'
            ]
        ];
    }

    /**
     * Test parsing of text content w/ HTML entities.
     *
     * @return void
     */
    public function testHtmlWithEntities()
    {
        $html = '<p>&lt;p&gt;</p>';
        $document = Parser::load($html);

        static::assertEquals(1, $document->childNodes->length);

        $pNode = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $pNode);
        static::assertEquals('p', $pNode->nodeName);
        static::assertEquals('&lt;p&gt;', $pNode->textContent);
        static::assertEquals(1, $pNode->childNodes->length);
        static::assertEquals(0, $pNode->attributes->length);

        $textNode = $pNode->childNodes->item(0);

        static::assertInstanceOf('DOMText', $textNode);
        static::assertEquals('&lt;p&gt;', $textNode->nodeValue);
    }

    /**
     * Test parsing of HTML content w/ HTML entities.
     *
     * @return void
     *
     * @see http://www-archive.mozilla.org/quality/browser/front-end/testcases/copy-paste/copy-entities/copy-entities.html
     */
    public function testHtmlDocumentWithEntities()
    {
        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="Content-Language" content="en">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <meta name="DC.contributor" content="G&eacute;rard Talbot">
  <meta name="DC.contributor" content="Beth Epperson">
  <title>Test Case for copy-entities</title>
  <link rel="up" href="copy-entities-d.html" title= "Test Case Description for copy-entities">
</head>
<body>
<h3>Latin Extended-A</h3>
<p>Note 1: <a name="oelig" id="oelig">ligature is a misnomer</a>, this is a separate character in some languages</p>
<table border="2" frame="border" rules="all" cellpadding="8">
    <thead>
    <tr>
        <th scope="col">Entity</th>
        <th scope="col">Code</th>
        <th scope="col">Named</th>
        <th scope="col">Coded</th>
        <th scope="col">Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td>OElig</td>
      <td>338</td>
      <td>"&amp;OElig;"</td>
      <td>"&OElig;"</td>
      <td>latin capital ligature OE</td>
    </tr>
    <tr>
      <td>scaron</td>
      <td>353</td>
      <td>"&amp;scaron;"</td>
      <td>"&scaron;"</td>
      <td>latin small letter s with caron</td>
    </tr>
  </tbody>
</table>
</body>
</html>
HTML;

        $document = Parser::load($html);

        static::assertEquals(1, $document->childNodes->length);

        $htmlNode = $document->childNodes->item(0);
        static::assertInstanceOf('DOMElement', $htmlNode);
        static::assertEquals('html', $htmlNode->nodeName);
        static::assertEquals(2, $htmlNode->childNodes->length - 1);
        static::assertEquals(1, $htmlNode->attributes->length);

        $bodyNode = $htmlNode->childNodes->item(2);

        static::assertInstanceOf('DOMElement', $bodyNode);
        static::assertEquals('body', $bodyNode->nodeName);
        static::assertEquals(0, $bodyNode->attributes->length);

        $h3Node = $bodyNode->childNodes->item(1);

        static::assertInstanceOf('DOMElement', $h3Node);
        static::assertEquals('h3', $h3Node->nodeName);
        static::assertEquals('Latin Extended-A', $h3Node->textContent);

        $tableNode = $bodyNode->childNodes->item(5);

        static::assertInstanceOf('DOMElement', $tableNode);
        static::assertEquals('table', $tableNode->nodeName);

        $tableBodyNode = $tableNode->childNodes->item(3);

        static::assertInstanceOf('DOMElement', $tableBodyNode);
        static::assertEquals('tbody', $tableBodyNode->nodeName);

        $tr1Node = $tableBodyNode->childNodes->item(1);

        static::assertInstanceOf('DOMElement', $tr1Node);
        static::assertEquals('tr', $tr1Node->nodeName);

        $td1Node = $tr1Node->childNodes->item(1);

        static::assertInstanceOf('DOMElement', $td1Node);
        static::assertEquals('td', $td1Node->nodeName);
        static::assertEquals('OElig', $td1Node->nodeValue);

        $td2Node = $tr1Node->childNodes->item(3);

        static::assertInstanceOf('DOMElement', $td2Node);
        static::assertEquals('td', $td2Node->nodeName);
        static::assertEquals('338', $td2Node->nodeValue);

        $td3Node = $tr1Node->childNodes->item(5);

        static::assertInstanceOf('DOMElement', $td3Node);
        static::assertEquals('td', $td3Node->nodeName);
        static::assertEquals('"&amp;OElig;"', $td3Node->nodeValue);

        $td4Node = $tr1Node->childNodes->item(7);

        static::assertInstanceOf('DOMElement', $td4Node);
        static::assertEquals('td', $td4Node->nodeName);
        static::assertEquals('"&OElig;"', $td4Node->nodeValue);

        $td5Node = $tr1Node->childNodes->item(9);

        static::assertInstanceOf('DOMElement', $td5Node);
        static::assertEquals('td', $td5Node->nodeName);
        static::assertEquals('latin capital ligature OE', $td5Node->nodeValue);
    }

    // TODO: Неверная обработка данных внутри атрибута.
    // Gumbo преобразовывает `1 &lt; 2 &LT; 3` в `1 < 2 < 3`.

//    /**
//     * Test parsing of attr content w/ HTML entities.
//     *
//     * @return void
//     */
//    public function testAttrWithEntities()
//    {
//        $html = '<p class="1 &lt; 2 &LT; 3"></p>';
//        $document = Parser::load($html);
//
//        static::assertEquals(1, $document->childNodes->length);
//       // static::assertEquals($html . chr(10), $document->saveHTML());
//
//        $node = $document->childNodes->item(0);
//
//        static::assertInstanceOf('DOMElement', $node);
//        static::assertEquals('p', $node->nodeName);
//        static::assertEquals(0, $node->childNodes->length);
//        static::assertEquals(1, $node->attributes->length);
//        static::assertEquals('1 &lt; 2 &LT; 3', $node->attributes->item(0)->nodeValue);
//    }
}
