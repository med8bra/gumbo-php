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

class InlineTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test parsing of CDATA in <textarea> elements.
     *
     * @void
     */
    public function testCDATA()
    {
        $html = <<<HTML
<textarea>
This is CDATA with <p>, <i> and <script> in it.
This should not trigger errors.
</textarea>
HTML;
        $text = <<<HTML
This is CDATA with <p>, <i> and <script> in it.
This should not trigger errors.
HTML;

        $document = Parser::load($html);

        static::assertEquals(1, $document->childNodes->length);

        $textArea = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $textArea);
        static::assertEquals($text . PHP_EOL, $textArea->textContent);
        static::assertEquals(0, $textArea->attributes->length);
    }


    /**
     * Test parsing of <script> tags.
     *
     * @param string $inputHtml
     * @param string $tagName
     * @param string $text
     *
     * @dataProvider inlineProvider
     *
     * @void
     */
    public function testInline($inputHtml, $tagName, $text)
    {
        $document = Parser::load($inputHtml);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);
        static::assertEquals($inputHtml . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $child = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $child);
        static::assertEquals($tagName, $child->nodeName);
        static::assertEquals($text, $child->textContent);
        static::assertEquals(0, $child->attributes->length);
    }

    /**
     * Provider for testInline().
     *
     * @return array
     */
    public function inlineProvider()
    {
        return [
            [
                '<script>x < 3;</script>',
                'script',
                'x < 3;'
            ],
            [
                '<script>alert("PWNED"); window.PWNED = true;</script>',
                'script',
                'alert("PWNED"); window.PWNED = true;'
            ],
            [
                "<style>div:before { content: '&lt;' attr(test) 'test'; content: \"&lt;\"; }</style>",
                'style',
                "div:before { content: '&lt;' attr(test) 'test'; content: \"&lt;\"; }"
            ],
            [
                "<style>div:before { content: 'let\\'s try \";\", eh?'; }</style>",
                'style',
                "div:before { content: 'let\\'s try \";\", eh?'; }"
            ],
            [
                "<style>@keyframes { 0% { opacity: 0; } 100% { opacity: 1.0; } } .test { opacity: 0; }</style>",
                'style',
                '@keyframes { 0% { opacity: 0; } 100% { opacity: 1.0; } } .test { opacity: 0; }'
            ],
            [
                '<style>/* hello */ {</style>',
                'style',
                '/* hello */ {'
            ]
        ];
    }
}
