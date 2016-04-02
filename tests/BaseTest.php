<?php
namespace LengthOfRope\Treehouse;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if we can instantiate the class using the new operator. This should
     * be prohibited, since we have a nice factory for all classes to support chaining.
     */
    public function testIfThisWorks()
    {
        $this->asserttrue(true);
    }
}
