<?php

/**
 * Test the Classname Supports Test.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2020 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Factory;

use IronBound\State\Factory\ClassSupportsTest;
use IronBound\State\Graph\GraphId;
use PHPUnit\Framework\TestCase;

class ClassSupportsTestTest extends TestCase
{
    public function test(): void
    {
        $test = new ClassSupportsTest('stdClass');
        $this->assertTrue($test(new \stdClass()));
        $this->assertTrue($test(new class extends \stdClass {
        }));

        $this->assertFalse($test(new GraphId('default')));
        $this->assertFalse($test(new class {
        }));
    }
}
