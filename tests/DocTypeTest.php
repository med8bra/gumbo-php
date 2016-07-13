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

class DocTypeTest extends \PHPUnit_Framework_TestCase
{

    // TODO: Doctype

    public function testDocType()
    {
        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<body>
<p>
    hi
    <a href="#">Moon</a>
</p>
</body>
</html>
HTML;

        $document = Parser::load($html);

        //static::assertEquals($html . chr(10), $document->saveHTML());
    }

    public function testWithDisabledNamespaces()
    {
        $html = '<!DOCTYPE html><html></html>';
        $document = Parser::load($html);

        //static::assertEquals($html . chr(10), $document->saveHTML());
    }
}
