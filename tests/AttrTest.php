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

class AttrTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test parsing of elements with attributes.
     *
     * @void
     */
    public function testAttr()
    {
        $html = '<a href="#" class="foo">Test</a>';
        $document = Parser::load($html);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);
        //static::assertEquals($html . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $child = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $child);
        static::assertEquals('a', $child->nodeName);
        static::assertEquals('Test', $child->textContent);

        // Tests for attributes.

        static::assertEquals(2, $child->attributes->length);

        $hrefAttr = $child->attributes->item(0);

        static::assertEquals('href', $hrefAttr->nodeName);
        static::assertEquals('#', $hrefAttr->nodeValue);
        $hrefAttr->nodeValue = null;

        $classAttr = $child->attributes->item(1);

        static::assertEquals('class', $classAttr->nodeName);
        static::assertEquals('foo', $classAttr->nodeValue);
    }

    /**
     * Test parsing of elements with boolean attributes.
     *
     * @void
     */
    public function testBooleanAttr()
    {
        $html = '<a readonly nowrap>Test</a>';
        $document = Parser::load($html);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);
        static::assertEquals($html . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $child = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $child);
        static::assertEquals('a', $child->nodeName);
        static::assertEquals('Test', $child->textContent);

        // Tests for attributes.

        static::assertEquals(2, $child->attributes->length);

        $hrefAttr = $child->attributes->item(0);

        static::assertEquals('readonly', $hrefAttr->nodeName);
        static::assertEquals('', $hrefAttr->nodeValue);

        $dataAttr = $child->attributes->item(1);

        static::assertEquals('nowrap', $dataAttr->nodeName);
        static::assertEquals('', $dataAttr->nodeValue);
    }

    // TODO: Обработка неверных аттрибутов.
    // Необходимо проверить специфицацию по именованию аттрибутов, они вроде соответствуют паттерну [\-a-z].

//    /**
//     * Test parsing of elements with invalid attribute names.
//     *
//     * @void
//     */
//    public function testInvalidAttr()
//    {
//        $html = '<a href+ data-content>Test</a>';
//        $document = Parser::load($html);
//
//        // Base tests.
//
//        static::assertEquals(1, $document->childNodes->length);
//        static::assertEquals($html . chr(10), $document->saveHTML());
//
//        // Tests for child nodes.
//
//        $child = $document->childNodes->item(0);
//
//        static::assertInstanceOf('DOMElement', $child);
//        static::assertEquals('a', $child->nodeName);
//        static::assertEquals('Test', $child->textContent);
//
//        // Tests for attributes.
//
//        static::assertEquals(2, $child->attributes->length);
//
//        $hrefAttr = $child->attributes->item(0);
//
//        static::assertEquals('href', $hrefAttr->nodeName);
//        static::assertEquals('', $hrefAttr->nodeValue);
//
//        $dataAttr = $child->attributes->item(1);
//
//        static::assertEquals('data-content', $dataAttr->nodeName);
//        static::assertEquals('', $dataAttr->nodeValue);
//    }

    /**
     * Test parsing <img src='bogus' onerror='prompt(document.domain)'>.
     *
     * @void
     */
    public function testOnClickAttr()
    {
        $html = "<img src='bogus' onclick='alert(\"test\")'>";
        $document = Parser::load($html);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);
        static::assertEquals("<img src=\"bogus\" onclick='alert(\"test\")'>" . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $child = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $child);
        static::assertEquals('img', $child->nodeName);
        static::assertEquals('', $child->textContent);

        // Tests for attributes.

        static::assertEquals(2, $child->attributes->length);

        $srcAttr = $child->attributes->item(0);

        static::assertEquals('src', $srcAttr->nodeName);
        static::assertEquals('bogus', $srcAttr->nodeValue);

        $onAttr = $child->attributes->item(1);

        static::assertEquals('onclick', $onAttr->nodeName);
        static::assertEquals('alert("test")', $onAttr->nodeValue);
    }

    /**
     * Test parsing <img src='bogus' onerror='prompt(document.domain)'>.
     *
     * @void
     */
    public function testPromptAttr()
    {
        $html = "<img src='bogus' onerror='prompt(document.domain)'>";
        $document = Parser::load($html);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);
        static::assertEquals(
            "<img src=\"bogus\" onerror=\"prompt(document.domain)\">" . chr(10),
            $document->saveHTML()
        );

        // Tests for child nodes.

        $child = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $child);
        static::assertEquals('img', $child->nodeName);
        static::assertEquals('', $child->textContent);

        // Tests for attributes.

        static::assertEquals(2, $child->attributes->length);

        $srcAttr = $child->attributes->item(0);

        static::assertEquals('src', $srcAttr->nodeName);
        static::assertEquals('bogus', $srcAttr->nodeValue);

        $onAttr = $child->attributes->item(1);

        static::assertEquals('onerror', $onAttr->nodeName);
        static::assertEquals('prompt(document.domain)', $onAttr->nodeValue);
    }

    /**
     * Test parsing xml* attrs.
     *
     * @return void
     */
    public function testXmlAttr()
    {
        $html = '<div xmlns:foo="http://example.com">FOO</div>';
        $document = Parser::load($html);

        static::assertEquals(1, $document->childNodes->length);
        static::assertEquals($html . chr(10), $document->saveHTML());

        $divNode = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $divNode);
        static::assertEquals('div', $divNode->nodeName);
        static::assertEquals('FOO', $divNode->textContent);

        $xmlAttr = $divNode->attributes->item(0);

        static::assertEquals('xmlns:foo', $xmlAttr->nodeName);
        static::assertEquals('http://example.com', $xmlAttr->nodeValue);
    }
}
