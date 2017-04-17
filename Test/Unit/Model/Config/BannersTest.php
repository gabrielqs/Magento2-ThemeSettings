<?php

namespace Gabrielqs\ThemeSettings\Test\Unit\Model\Config;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\ThemeSettings\Model\Config\Banners as Subject;

/**
 * Unit Testcase
 */
class BannersTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * @var Subject
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->className = '\Gabrielqs\ThemeSettings\Model\Config\Banners';
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(null)
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);
        return $arguments;
    }

    public function testToOptionArrayShouldReturnArray()
    {
        $this->assertInternalType('array', $this->originalSubject->toOptionArray());
        $this->assertEquals(5, count($this->originalSubject->toOptionArray()));
        $this->assertEquals([
            [
                'value' => 1,
                'label' => __('Banner %1', 1)
            ],
            [
                'value' => 2,
                'label' => __('Banner %1', 2)
            ],
            [
                'value' => 3,
                'label' => __('Banner %1', 3)
            ],
            [
                'value' => 4,
                'label' => __('Banner %1', 4)
            ],
            [
                'value' => 5,
                'label' => __('Banner %1', 5)
            ]
        ], $this->originalSubject->toOptionArray());
    }
}