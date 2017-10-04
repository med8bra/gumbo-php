<?php
/**
 * Copyright 2017 Alexander Fedyashov
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

class WhitespaceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test parsing of HTML with <pre> element.
     *
     * @void
     */
    public function testNewLine()
    {
        $html =
            '<html><body><pre><span>Line 1</span>
    <span>Line 2</span>
<span>Line 3</span>
</pre><pre>Line 1
    Line 2
Line 3
</pre></body></html>';
        $document = Parser::load($html);

        static::assertEquals($html . chr(10), $document->saveHTML());
    }
}
