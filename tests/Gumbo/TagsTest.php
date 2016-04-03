<?php

namespace Layershifter\Gumbo\Tests;

use Layershifter\Gumbo;

/**
 * Basic tests for tags.
 */
class TagsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Parse and serialize a string.
     *
     * @param string $html HTML fragment
     *
     * @return string
     */
    private function cycle($html)
    {
        $dom = Gumbo::load('<!DOCTYPE html><html><body>' . $html . '</body></html>');

        return $dom->saveHTML($dom);
    }

    /**
     * Test entity encoding.
     *
     * @return void
     */
    public function testEntities()
    {
        $result = self::cycle('<a>Apples &amp; bananas.</a>');

        self::assertRegExp('|Apples &amp; bananas.|', $result);

        $result = self::cycle('<p>R&D</p>');

        self::assertRegExp('|R&amp;D|', $result);
    }

    /**
     * Test for inline comments.
     *
     * @return void
     */
    public function testComment()
    {
        $result = self::cycle('a<!-- This is a test comment. -->b');

        self::assertRegExp('|<!-- This is a test comment. -->|', $result);
    }

    /**
     * Test for CDATA.
     *
     * @return void
     */
    public function testCDATA()
    {
        $res = self::cycle('<div id="cDataExample">
<![CDATA[
CDATA test < > & "
]]>
</div>');

        self::assertRegExp('|<!\[CDATA\[ This <is> a test\. \]\]>|', $res);
    }

    /**
     * Test for namespaces.
     *
     * @return void
     * */
    public function testNamespace()
    {
        $dom = Gumbo::load('<t:tag/>');

        self::assertInstanceOf('\DOMDocument', $dom);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace("t", "http://example.com");

        self::assertEquals(1, $xpath->query("//t:tag")->length);
    }

    /**
     * Test for SVG.
     *
     * @return void
     */
    public function testSvg()
    {
        $dom = Gumbo::load(
            '<!doctype html>
      <html lang="en">
        <body>
          <div id="foo" class="bar baz">foo bar baz</div>
          <svg width="150" height="100" viewBox="0 0 3 2">
            <rect width="1" height="2" x="0" fill="#008d46" />
            <rect width="1" height="2" x="1" fill="#ffffff" />
            <rect width="1" height="2" x="2" fill="#d2232c" />
            <text font-family="Verdana" font-size="32">
              <textPath xlink:href="#Foo">
                Test Text.
              </textPath>
            </text>
          </svg>
        </body>
      </html>');

        // Test a mixed case attribute.
        $list = $dom->getElementsByTagName('svg');
        self::assertNotEmpty($list->length);
        $svg = $list->item(0);
        self::assertEquals("0 0 3 2", $svg->getAttribute('viewBox'));
        self::assertFalse($svg->hasAttribute('viewbox'));

        // Test a mixed case tag.
        // Note: getElementsByTagName is not case sensetitive.
        $list = $dom->getElementsByTagName('textPath');
        self::assertNotEmpty($list->length);
        $textPath = $list->item(0);
        self::assertEquals('textPath', $textPath->tagName);
        self::assertNotEquals('textpath', $textPath->tagName);

        $html = $dom->saveHTML($dom);
        self::assertRegExp('|<svg width="150" height="100" viewBox="0 0 3 2">|', $html);
        self::assertRegExp('|<rect width="1" height="2" x="0" fill="#008d46" />|', $html);
    }

    /**
     *
     */
    public function testMathMl()
    {
        $dom = Gumbo::load(
            '<!doctype html>
      <html lang="en">
        <body>
          <div id="foo" class="bar baz" definitionURL="http://example.com">foo bar baz</div>
          <math>
            <mi>x</mi>
            <csymbol definitionURL="http://www.example.com/mathops/multiops.html#plusminus">
              <mo>&PlusMinus;</mo>
            </csymbol>
            <mi>y</mi>
          </math>
        </body>
      </html>');

        $list = $dom->getElementsByTagName('math');
        self::assertNotEmpty($list->length);

        $list = $dom->getElementsByTagName('div');
        self::assertNotEmpty($list->length);
        $div = $list->item(0);
        self::assertEquals('http://example.com', $div->getAttribute('definitionurl'));
        self::assertFalse($div->hasAttribute('definitionURL'));
        $list = $dom->getElementsByTagName('csymbol');
        $csymbol = $list->item(0);
        self::assertEquals('http://www.example.com/mathops/multiops.html#plusminus', $csymbol->getAttribute('definitionURL'));
        self::assertFalse($csymbol->hasAttribute('definitionurl'));

        $html = $dom->saveHTML($dom);
        self::assertRegExp('|<csymbol definitionURL="http://www.example.com/mathops/multiops.html#plusminus">|', $html);
        self::assertRegExp('|<mi>y</mi>|', $html);
    }

    /**
     *
     */
    public function testUnknownElements()
    {
        $dom = Gumbo::load(
            "<f:rug>
      <f:name>Big rectangle thing</f:name>
      <f:width>40</f:width>
      <f:length>80</f:length>
    </f:rug>
    <sarcasm>um, yeah</sarcasm>");


        $markup = $dom->saveHTML($dom);
        self::assertRegExp('|<f:name>Big rectangle thing</f:name>|', $markup);
        self::assertRegExp('|<sarcasm>um, yeah</sarcasm>|', $markup);
    }

    /**
     *
     */
    public function testElements()
    {
        // Should have content.
        $res = self::cycle('<div>FOO</div>');
        self::assertRegExp('|<div>FOO</div>|', $res);

        // Should be empty
        $res = self::cycle('<span></span>');
        self::assertRegExp('|<span></span>|', $res);

        // Should have content.
        $res = self::cycle('<div>FOO</div>');
        self::assertRegExp('|<div>FOO</div>|', $res);

        // Should be empty
        $res = self::cycle('<span></span>');
        self::assertRegExp('|<span></span>|', $res);

        // Elements with dashes and underscores
        $res = self::cycle('<sp-an></sp-an>');
        self::assertRegExp('|<sp-an></sp-an>|', $res);
        $res = self::cycle('<sp_an></sp_an>');
        self::assertRegExp('|<sp_an></sp_an>|', $res);

        // Should have no closing tag.
        $res = self::cycle('<hr>');
        self::assertRegExp('|<hr></body>|', $res);
    }

    /**
     *
     */
    public function testAttributes()
    {
        $res = self::cycle('<div attr="val">FOO</div>');
        self::assertRegExp('|<div attr="val">FOO</div>|', $res);

        // XXX: Note that spec does NOT require attrs in the same order.
        $res = self::cycle('<div attr="val" class="even">FOO</div>');
        self::assertRegExp('|<div attr="val" class="even">FOO</div>|', $res);

        $res = self::cycle('<div xmlns:foo="http://example.com">FOO</div>');
        self::assertRegExp('|<div xmlns:foo="http://example.com">FOO</div>|', $res);
    }

    /**
     *
     */
    public function testPCData()
    {
        $res = self::cycle('<a>This is a test.</a>');
        self::assertRegExp('|This is a test.|', $res);

        $res = self::cycle('This
      is
      a
      test.');

        // Check that newlines are there, but don't count spaces.
        self::assertRegExp('|This\n\s*is\n\s*a\n\s*test.|', $res);

        $res = self::cycle('<a>This <em>is</em> a test.</a>');
        self::assertRegExp('|This <em>is</em> a test.|', $res);
    }

    /**
     *
     */
    public function testUnescaped()
    {
        $res = self::cycle('<script>2 < 1</script>');
        self::assertRegExp('|2 < 1|', $res);

        $res = self::cycle('<style>div>div>div</style>');
        self::assertRegExp('|div>div>div|', $res);
    }

    /**
     * Test for saving fragment.
     *
     * @return void
     */
    public function testSaveHTMLFragment()
    {
        $fragment = '<section id="Foo"><div class="Bar">Baz</div></section>';
        $dom = Gumbo::load($fragment);

        $string = $dom->saveHTML($dom);
        self::assertEquals($fragment, $string);
    }
}
