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
class MemTest extends \PHPUnit_Framework_TestCase
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
    public function testMemoryFreed()
    {
        // load one doc (warming)
        Parser::load(self::getSimple());
        gc_collect_cycles();
        
        $memory_usage_start = memory_get_usage();

        for ($i=0; $i < 100 ; $i++) 
            Parser::load(self::getSimple());

        // force gc to run
        gc_collect_cycles();
        $memory_usage_end = memory_get_usage();

        self::assertEquals($memory_usage_start,$memory_usage_end);
    }
}
