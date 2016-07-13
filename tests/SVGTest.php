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

class SVGTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test parsing of SVG elements.
     *
     * @void
     */
    public function testSVG()
    {
        $html = <<<HTML
<svg width='100' height='200' viewbox='0 0 100 100'><path d='M 0 0 L 100 0 L 100 100 L 0 100 Z'/></svg>
HTML;
        $document = Parser::load($html);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);

        // TODO: SVG xmlns
        //static::assertEquals($html . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $svg = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $svg);
        static::assertEquals('svg', $svg->nodeName);
        static::assertEquals('', $svg->textContent);
        static::assertEquals(1, $svg->childNodes->length);
        static::assertEquals(3, $svg->attributes->length);

        $path = $svg->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $path);
        static::assertEquals('path', $path->nodeName);
        static::assertEquals(1, $path->attributes->length);

        // Tests for attributes.

        $widthAttr = $svg->attributes->item(0);

        static::assertEquals('width', $widthAttr->nodeName);
        static::assertEquals('100', $widthAttr->nodeValue);

        $heightAttr = $svg->attributes->item(1);

        static::assertEquals('height', $heightAttr->nodeName);
        static::assertEquals('200', $heightAttr->nodeValue);

        $viewBoxAttr = $svg->attributes->item(2);

        // TODO: Attr naming

        static::assertEquals('viewbox', $viewBoxAttr->nodeName);
        static::assertEquals('0 0 100 100', $viewBoxAttr->nodeValue);

        $dAttr = $path->attributes->item(0);

        static::assertEquals('d', $dAttr->nodeName);
        static::assertEquals('M 0 0 L 100 0 L 100 100 L 0 100 Z', $dAttr->nodeValue);
    }

    // TODO: Attributes

//    /**
//    * @dataProvider svgAttrProvider
//     */
//    public function testSvgAttr($inputHtml, $attr)
//    {
//        $document = Parser::load($inputHtml);
//
//        // Base tests.
//
//        static::assertEquals(1, $document->childNodes->length);
//
//        // TODO: SVG xmlns
//        static::assertEquals($inputHtml . chr(10), $document->saveHTML());
//    }

    /**
     * Provider for testSvgAttr() method.
     */
    public function svgAttrProvider()
    {
        return [
            [
                '<svg xmlns="foo"></svg>',
                'xmlns'
            ],
            [
                '<svg xmlns:foo="foo"></svg>',
                'xmlns:foo'
            ],
            [
                '<svg xmlns:xlink="foo"></svg>',
                'xmlns:xlink'
            ], [
                '<svg xlink:href="foo"></svg>',
                'xlink:href'
            ]
        ];
    }

    /**
     * Test verifying SVG namespace.
     *
     * @void
     */
    public function testSVGNamespace()
    {
        $html = <<<HTML
<html><body><p>start test</p><svg width='100' height='200' viewbox='0 0 100 100'><path d='M 0 0 L 100 0 L 100 100 L 0 100 Z'/></svg><p>end test</p></body></html>
HTML;
        $document = Parser::load($html);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);

        // TODO: SVG xmlns
        //static::assertEquals($html . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $html = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $html);
        static::assertEquals('html', $html->nodeName);
        static::assertEquals('start testend test', $html->textContent);
        static::assertEquals(1, $html->childNodes->length);
        static::assertEquals(0, $html->attributes->length);

        $body = $html->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $body);
        static::assertEquals('body', $body->nodeName);
        static::assertEquals('start testend test', $body->textContent);
        static::assertEquals(3, $body->childNodes->length);
        static::assertEquals(0, $body->attributes->length);

        $pStart = $body->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $pStart);
        static::assertEquals('p', $pStart->nodeName);
        static::assertEquals('start test', $pStart->textContent);
        static::assertEquals(1, $pStart->childNodes->length);
        static::assertEquals(0, $pStart->attributes->length);

        $svg = $body->childNodes->item(1);

        static::assertInstanceOf('DOMElement', $svg);
        static::assertEquals('svg', $svg->nodeName);
        static::assertEquals('', $svg->textContent);
        static::assertEquals(1, $svg->childNodes->length);
        static::assertEquals(3, $svg->attributes->length);

        $path = $svg->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $path);
        static::assertEquals('path', $path->nodeName);
        static::assertEquals(1, $path->attributes->length);

        $pEnd = $body->childNodes->item(2);

        static::assertInstanceOf('DOMElement', $pEnd);
        static::assertEquals('p', $pEnd->nodeName);
        static::assertEquals('end test', $pEnd->textContent);
        static::assertEquals(1, $pEnd->childNodes->length);
        static::assertEquals(0, $pEnd->attributes->length);

        // Tests for attributes.

        $widthAttr = $svg->attributes->item(0);

        static::assertEquals('width', $widthAttr->nodeName);
        static::assertEquals('100', $widthAttr->nodeValue);

        $heightAttr = $svg->attributes->item(1);

        static::assertEquals('height', $heightAttr->nodeName);
        static::assertEquals('200', $heightAttr->nodeValue);

        $viewBoxAttr = $svg->attributes->item(2);
        static::assertEquals('viewbox', $viewBoxAttr->nodeName);
        static::assertEquals('0 0 100 100', $viewBoxAttr->nodeValue);

        $dAttr = $path->attributes->item(0);

        static::assertEquals('d', $dAttr->nodeName);
        static::assertEquals('M 0 0 L 100 0 L 100 100 L 0 100 Z', $dAttr->nodeValue);
    }

    /**
     * Test parsing of SVG elements.
     *
     * @void
     */
    public function testSVGError()
    {
        $html = <<<HTML
<html><body><svg><rect/></svg><path>error</path></body></html>
HTML;
        $document = Parser::load($html);

        // Base tests.

        static::assertEquals(1, $document->childNodes->length);

        // TODO: SVG xmlns
        //static::assertEquals($html . chr(10), $document->saveHTML());

        // Tests for child nodes.

        $html = $document->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $html);
        static::assertEquals('html', $html->nodeName);
        static::assertEquals('error', $html->textContent);
        static::assertEquals(1, $html->childNodes->length);
        static::assertEquals(0, $html->attributes->length);

        $body = $html->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $body);
        static::assertEquals('body', $body->nodeName);
        static::assertEquals('error', $body->textContent);
        static::assertEquals(2, $body->childNodes->length);
        static::assertEquals(0, $body->attributes->length);

        $svg = $body->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $svg);
        static::assertEquals('svg', $svg->nodeName);
        static::assertEquals('', $svg->textContent);
        static::assertEquals(1, $svg->childNodes->length);
        static::assertEquals(0, $svg->attributes->length);

        $rect = $svg->childNodes->item(0);

        static::assertInstanceOf('DOMElement', $rect);
        static::assertEquals('rect', $rect->nodeName);
        static::assertEquals('', $rect->textContent);
        static::assertEquals(0, $rect->childNodes->length);
        static::assertEquals(0, $rect->attributes->length);

        $path = $body->childNodes->item(1);

        static::assertInstanceOf('DOMElement', $path);
        static::assertEquals('path', $path->nodeName);
        static::assertEquals('error', $path->textContent);
        static::assertEquals(0, $path->attributes->length);
    }
}
