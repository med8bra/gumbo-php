<?php

namespace Layershifter\Gumbo\Tests;

use Layershifter\Gumbo;

/**
 * Basic tests for parser.
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
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
        $DOMDocument = Gumbo::load(self::getSimple());

        self::assertInstanceOf('\DOMDocument', $DOMDocument);
    }

    /**
     * Test for loading fragment.
     *
     * @return void
     */
    public function testLoadHTMLFragment()
    {
        $htmlFragment = '<section id="Foo"><div class="Bar">Baz</div></section>';
        $DOMDocument = Gumbo::load($htmlFragment);

        self::assertInstanceOf('\DOMDocument', $DOMDocument);
    }

    /**
     * Test for saving HTML.
     *
     * @return void
     */
    public function testSaveHTML()
    {
        $DOMDocument = Gumbo::load(self::getSimple());
        $savedHTML = $DOMDocument->saveHTML();

        self::assertInstanceOf('\DOMDocument', $DOMDocument);

        self::assertRegExp('|<title>I am a title example</title>|', $savedHTML);
        self::assertRegExp('|<p>This is a test.</p>|', $savedHTML);
    }

    /**
     * This test reads a document into a dom, turn the dom into a document, then tries to read that document again. This
     * makes sure we are reading, and generating a document that works at a high level.
     *
     * @return void
     */
    public function testItWorks()
    {
        $DOMDocument = Gumbo::load(self::getSimple());
        $savedHTML = $DOMDocument->saveHTML($DOMDocument);

        $secondDOMDocument = Gumbo::load($savedHTML);

        self::assertInstanceOf('\DOMDocument', $DOMDocument);
        self::assertInstanceOf('\DOMDocument', $secondDOMDocument);
    }

    /**
     * Test for illegal html.
     *
     * @return void
     * */
    public function testErrors()
    {
        $DOMDocument = Gumbo::load('<xx as>');

        self::assertInstanceOf('\DOMDocument', $DOMDocument);
    }
}
