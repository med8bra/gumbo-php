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

/**
 * Tests for parser loader.
 */
class LoadTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Returns simple html for test.
     *
     * @return string
     */
    private static function getSimple()
    {
        return file_get_contents(__DIR__ . '/html/simple.html');
    }

    /**
     * Test for loading HTML.
     *
     * @return void
     */
    public function testLoadHTML()
    {
        self::assertInstanceOf('DOMDocument', Parser::load(self::getSimple()));
    }

    /**
     * Test for loading fragment.
     *
     * @return void
     */
    public function testLoadHTMLFragment()
    {
        $html = '<section id="Foo"><div class="Bar">Baz</div></section>';

        self::assertInstanceOf('DOMDocument', Parser::load($html));
    }

    /**
     * Test for loading empty string.
     *
     * @return void
     */
    public function testLoadEmpty()
    {
        $document = Parser::load('');

        self::assertInstanceOf('\DOMDocument', $document);
        self::assertEquals('' . chr(10), $document->saveHTML());
    }

    /**
     * Test for saving HTML.
     *
     * @return void
     */
    public function testSaveHTML()
    {
        $document = Parser::load(self::getSimple());

        self::assertInstanceOf('DOMDocument', $document);

        $savedHTML = $document->saveHTML();

        self::assertRegExp('|<title>I am a title example</title>|', $savedHTML);
        self::assertRegExp('|<p>This is a test.</p>|', $savedHTML);
    }

    /**
     * This test reads a document into a dom, turn the dom into a document, then tries to read that document again. This
     * makes sure we are reading, and generating a document that works at a high level.
     *
     * @return void
     */
    public function testLoadAfterSave()
    {
        $firstDocument = Parser::load(self::getSimple());
        $secondDocument = Parser::load($firstDocument->saveHTML($firstDocument));

        self::assertInstanceOf('DOMDocument', $firstDocument);
        self::assertInstanceOf('DOMDocument', $secondDocument);
    }
}
