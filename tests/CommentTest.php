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

class CommentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test parsing of HTML comment.
     *
     * @void
     */
    public function testBasicComment()
    {
        $html = 'hi<!--testing-->there';
        $document = Parser::load($html);

        // Base tests.

        static::assertEquals(3, $document->childNodes->length);
        static::assertEquals($html . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $firstChild = $document->childNodes->item(0);

        static::assertInstanceOf('DOMText', $firstChild);
        static::assertEquals('hi', $firstChild->textContent);

        $secondChild = $document->childNodes->item(1);

        static::assertInstanceOf('DOMComment', $secondChild);
        static::assertEquals('testing', $secondChild->textContent);

        $thirdChild = $document->childNodes->item(2);

        static::assertInstanceOf('DOMText', $thirdChild);
        static::assertEquals('there', $thirdChild->textContent);
    }

    /**
     * Test parsing of HTML comment.
     *
     * @param string $inputHtml
     *
     * @dataProvider commentsProvider
     *
     * @void
     */
    public function testComments($inputHtml)
    {
        $document = Parser::load($inputHtml);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);
        static::assertEquals($inputHtml . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $pNode = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $pNode);
        static::assertEquals('p', $pNode->nodeName);
        static::assertEquals('hi ', $pNode->textContent);
        static::assertEquals(2, $pNode->childNodes->length);

        $textNode = $pNode->childNodes->item(0);

        static::assertInstanceOf('DOMText', $textNode);
        static::assertEquals('hi ', $textNode->textContent);

        $commentNode = $pNode->childNodes->item(1);

        static::assertInstanceOf('DOMComment', $commentNode);
        static::assertEquals('world', trim($commentNode->textContent));
    }

    /**
     * Data provider for testComments().
     *
     * @return array
     */
    public function commentsProvider()
    {
        return [
            ['<p>hi <!--world--></p>'],
            ['<p>hi <!-- world--></p>'],
            ['<p>hi <!--world --></p>'],
            ['<p>hi <!-- world --></p>']
        ];
    }

    // TODO: Сложные комментарии.
    // Gumbo игнорирует первую секцию комментариев.

//    /**
//     * Test parsing of HTML comments with '--' in them.
//     *
//     * @void
//     */
//    public function testComplexComment()
//    {
//        $html = <<<HTML
//<!--
//     first section
//-->
//<a></a>
//<!--
//     second section
//-->
//HTML;
//        $document = Parser::load($html);
//
//        // Base tests.
//
//        //     static::assertEquals(2, $document->childNodes->length);
//        static::assertEquals($html . chr(10), $document->saveHTML());
//
//        // Tests for child nodes.
//
//        $child = $document->childNodes->item(0);
//
//        static::assertInstanceOf('DOMComment', $child);
//        static::assertEquals('allow\n--\nin comments plz', $child->textContent);
//        static::assertEquals(0, $child->attributes->length);
//    }
}
