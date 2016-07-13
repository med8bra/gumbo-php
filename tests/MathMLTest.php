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

class MathMLTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test parsing of SVG elements.
     *
     * @void
     */
    public function testMathML()
    {
        $html = <<<HTML
<math xmlns="http://www.w3.org/1998/Math/MathML">
<matrix>
  <matrixrow>
    <cn> 0 </cn> <cn> 1 </cn> <cn> 0 </cn>
  </matrixrow>
  <matrixrow>
    <cn> 0 </cn> <cn> 0 </cn> <cn> 1 </cn>
  </matrixrow>
  <matrixrow>
    <cn> 1 </cn> <cn> 0 </cn> <cn> 0 </cn>
  </matrixrow>
</matrix>
</math>
HTML;
        $document = Parser::load($html);

        // Base tests.
//
//        static::assertEquals(1, $document->childNodes->length);
//
//        // TODO: SVG xmlns
//        //static::assertEquals($html . chr(10), $document->saveHTML());
//
//        // Tests for child nodes.
//
//        $svg = $document->childNodes->item(0);
//
//        static::assertInstanceOf('DOMElement', $svg);
//        static::assertEquals('svg', $svg->nodeName);
//        static::assertEquals('', $svg->textContent);
//        static::assertEquals(1, $svg->childNodes->length);
//        static::assertEquals(3, $svg->attributes->length);
//
//        $path = $svg->childNodes->item(0);
//
//        static::assertInstanceOf('DOMElement', $path);
//        static::assertEquals('path', $path->nodeName);
//        static::assertEquals(1, $path->attributes->length);
//
//        // Tests for attributes.
//
//        $widthAttr = $svg->attributes->item(0);
//
//        static::assertEquals('width', $widthAttr->nodeName);
//        static::assertEquals('100', $widthAttr->nodeValue);
//
//        $heightAttr = $svg->attributes->item(1);
//
//        static::assertEquals('height', $heightAttr->nodeName);
//        static::assertEquals('200', $heightAttr->nodeValue);
//
//        $viewBoxAttr = $svg->attributes->item(2);
//
//        // TODO: Attr naming
//
//        //static::assertEquals('viewbox', $viewBoxAttr->nodeName);
//        static::assertEquals('0 0 100 100', $viewBoxAttr->nodeValue);
//
//        $dAttr = $path->attributes->item(0);
//
//        static::assertEquals('d', $dAttr->nodeName);
//        static::assertEquals('M 0 0 L 100 0 L 100 100 L 0 100 Z', $dAttr->nodeValue);
    }
}
