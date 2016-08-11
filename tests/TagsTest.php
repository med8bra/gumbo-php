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

class TagsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test parsing case sensitive elements.
     *
     * @void
     */
    public function testCaseSensitive()
    {
        $inputHtml = <<<HTML
<P CLASS="FOO">hi</P><P class-Name="FOO">hi</P><p classNaME="FOO">hi</P><P class="FOO">hi</p>
HTML;

        $outHtml = <<<HTML
<P CLASS="FOO">hi</P><P class-Name="FOO">hi</P><p classNaME="FOO">hi</p><P class="FOO">hi</P>
HTML;

        $document = Parser::load($inputHtml);

        // Base tests.

        static::assertEquals(4, $document->childNodes->length);
        static::assertEquals($outHtml . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $child = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $child);
        static::assertEquals('P', $child->nodeName);
        static::assertEquals('hi', $child->textContent);
        static::assertEquals(1, $child->attributes->length);
    }

    /**
     * Test parsing of custom tags.
     *
     * @void
     */
    public function testCustomTag()
    {
        $html = '<p-q style="color: green;">hello</p-q>';
        $document = Parser::load($html);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);
        static::assertEquals($html . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $child = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $child);
        static::assertEquals('p-q', $child->nodeName);
        static::assertEquals('hello', $child->textContent);

        // Tests for attributes.

        static::assertEquals(1, $child->attributes->length);

        $attr = $child->attributes->item(0);

        static::assertEquals('style', $attr->nodeName);
        static::assertEquals('color: green;', $attr->nodeValue);
    }

    /**
     * Test parsing of invalid tags.
     *
     * @void
     */
    public function testInvalidCustomTag()
    {
        $document = Parser::load('<-></->');

        static::assertEquals(2, $document->childNodes->length);
        static::assertEquals('&lt;-&gt;<!----->' . chr(10), $document->saveHTML());
    }

    /**
     * Test parsing form.
     *
     * @void
     */
    public function testForm()
    {
        $html = <<<HTML
<form><input type="text" name="submit" value="Submit!"><input type="text" name="style"></form>
HTML;
        $document = Parser::load($html);

        static::assertEquals(1, $document->childNodes->length);
        static::assertEquals($html . chr(10), $document->saveHTML());

        $form = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $form);
        static::assertEquals('form', $form->nodeName);
        static::assertEquals('', $form->textContent);
        static::assertEquals(2, $form->childNodes->length);
        static::assertEquals(0, $form->attributes->length);

        $firstInput = $form->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $firstInput);
        static::assertEquals('input', $firstInput->nodeName);
        static::assertEquals('', $firstInput->textContent);
        static::assertEquals(0, $firstInput->childNodes->length);
        static::assertEquals(3, $firstInput->attributes->length);

        $secondInput = $form->childNodes->item(1);

        static::assertInstanceOf('DOMElement', $secondInput);
        static::assertEquals('input', $secondInput->nodeName);
        static::assertEquals('', $secondInput->textContent);
        static::assertEquals(0, $secondInput->childNodes->length);
        static::assertEquals(2, $secondInput->attributes->length);

        $attr = $firstInput->attributes->item(2);

        static::assertEquals('value', $attr->nodeName);
        static::assertEquals('Submit!', $attr->nodeValue);
    }

    /**
     * Test parsing of self-closing void elements.
     *
     * @param string $inputHtml
     *
     * @dataProvider selfClosingTagsProvider
     *
     * @void
     */
    public function testSelfClosingTags($inputHtml)
    {
        $document = Parser::load($inputHtml);

        // Base tests.

        static::assertEquals(2, $document->childNodes->length);
        static::assertEquals('hello<br>' . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $textNode = $document->childNodes->item(0);

        static::assertInstanceOf('DOMText', $textNode);
        static::assertEquals('hello', $textNode->textContent);

        $brNode = $document->childNodes->item(1);

        static::assertInstanceOf('DOMElement', $brNode);
        static::assertEquals('br', $brNode->nodeName);
        static::assertEquals('', $brNode->textContent);
    }

    /**
     * Provider for testSelfClosingTags.
     *
     * @return array
     */
    public function selfClosingTagsProvider()
    {
        return [
            ['hello<br/>'],
            ['hello<br />'],
            ['hello<br >']
        ];
    }

    /**
     * Test parsing of valid HTML.
     *
     * @void
     */
    public function testValidHtml()
    {
        $html = '<p class="foo">hello there</p>';
        $document = Parser::load($html);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);
        static::assertEquals($html . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $child = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $child);
        static::assertEquals('p', $child->nodeName);
        static::assertEquals('hello there', $child->textContent);

        // Tests for attributes.

        static::assertEquals(1, $child->attributes->length);

        $attr = $child->attributes->item(0);

        static::assertEquals('class', $attr->nodeName);
        static::assertEquals('foo', $attr->nodeValue);
    }

    /**
     * Test parsing of valid HTML from sample file.
     *
     * @void
     */
    public function testValidHtmlFromFile()
    {
        $html = file_get_contents(__DIR__ . '/html/full.html');
        $document = Parser::load($html);

        static::assertEquals(1, $document->childNodes->length);

        $htmlNode = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $htmlNode);
        static::assertEquals('html', $htmlNode->nodeName);
        static::assertEquals(2, $htmlNode->childNodes->length);
        static::assertEquals(1, $htmlNode->attributes->length);

        $bodyNode = $htmlNode->childNodes->item(1);
        $aNode = $bodyNode->childNodes->item(1);

        static::assertInstanceOf('DOMElement', $aNode);
        static::assertEquals('a', $aNode->nodeName);
        static::assertEquals('The Daily Puppy', $aNode->textContent);
        static::assertEquals(1, $aNode->childNodes->length);
        static::assertEquals(2, $aNode->attributes->length);

        $hrefAttr = $aNode->attributes->item(0);

        static::assertInstanceOf('DOMAttr', $hrefAttr);
        static::assertEquals('href', $hrefAttr->nodeName);
        static::assertEquals('http://dailypuppy.com', $hrefAttr->nodeValue);

        $onClickAttr = $aNode->attributes->item(1);

        static::assertInstanceOf('DOMAttr', $onClickAttr);
        static::assertEquals('onclick', $onClickAttr->nodeName);
        static::assertEquals('return leave()', $onClickAttr->nodeValue);
    }
}
