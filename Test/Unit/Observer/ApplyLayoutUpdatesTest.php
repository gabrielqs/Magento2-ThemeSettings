<?php

namespace Gabrielqs\ThemeSettings\Test\Observer;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\ThemeSettings\Observer\ApplyLayoutUpdates as Subject;
use \Magento\Framework\View\Page\Config as PageConfig;
use \Magento\Framework\App\Config;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\View\Result\Page;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\View\Layout;
use \Magento\Framework\View\Model\Layout\Merge as Update;
use \Magento\Framework\App\Request\Http;

/**
 * Unit Testcase
 */
class ApplyLayoutUpdatesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var Layout
     */
    protected $layout = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * Observer
     * @var Observer
     */
    protected $observer;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * @var Page
     */
    protected $page = null;

    /**
     * Request
     * @var Http
     */
    protected $request;

    /**
     * @var Config
     */
    protected $scopeConfig = null;

    /**
     * @var Subject
     */
    protected $subject = null;

    /**
     * @var Update
     */
    protected $update = null;


    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->className = Subject::class;
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(null)
            ->getMock();

        $this->layout = $this
            ->getMockBuilder(Layout::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUpdate'])
            ->getMock();

        $this->update = $this
            ->getMockBuilder(Update::class)
            ->disableOriginalConstructor()
            ->setMethods(['addHandle'])
            ->getMock();

        $this->observer = $this
            ->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getData'])
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->scopeConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMock();
        $arguments['scopeConfig'] = $this->scopeConfig;

        $this->request = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFullActionName'])
            ->getMock();
        $arguments['request'] = $this->request;

        return $arguments;
    }

    public function dataProviderTestAfterAddPageLayoutHandles()
    {
        return [
            [
                '', Subject::FOOTER_LAYOUT_HANDLE_PREFIX . '1',
                '', Subject::HEADER_LAYOUT_HANDLE_PREFIX . '1',
                '', Subject::PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX . '1',
                '', Subject::BANNER_LAYOUT_HANDLE_PREFIX . '1',
            ],
            [
                1, Subject::FOOTER_LAYOUT_HANDLE_PREFIX . '1',
                1, Subject::HEADER_LAYOUT_HANDLE_PREFIX . '1',
                1, Subject::PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX . '1',
                1, Subject::BANNER_LAYOUT_HANDLE_PREFIX . '1',
            ],
            [
                2, Subject::FOOTER_LAYOUT_HANDLE_PREFIX . '2',
                2, Subject::HEADER_LAYOUT_HANDLE_PREFIX . '2',
                2, Subject::PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX . '2',
                2, Subject::BANNER_LAYOUT_HANDLE_PREFIX . '2',
            ],
            [
                3, Subject::FOOTER_LAYOUT_HANDLE_PREFIX . '3',
                3, Subject::HEADER_LAYOUT_HANDLE_PREFIX . '3',
                3, Subject::PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX . '3',
                3, Subject::BANNER_LAYOUT_HANDLE_PREFIX . '3',
            ],
            [
                4, Subject::FOOTER_LAYOUT_HANDLE_PREFIX . '4',
                4, Subject::HEADER_LAYOUT_HANDLE_PREFIX . '4',
                4, Subject::PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX . '4',
                4, Subject::BANNER_LAYOUT_HANDLE_PREFIX . '4',
            ],
            [
                5, Subject::FOOTER_LAYOUT_HANDLE_PREFIX . '5',
                5, Subject::HEADER_LAYOUT_HANDLE_PREFIX . '5',
                5, Subject::PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX . '5',
                5, Subject::BANNER_LAYOUT_HANDLE_PREFIX . '5',
            ],
            [
                6, Subject::FOOTER_LAYOUT_HANDLE_PREFIX . '6',
                6, Subject::HEADER_LAYOUT_HANDLE_PREFIX . '6',
                6, Subject::PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX . '6',
                6, Subject::BANNER_LAYOUT_HANDLE_PREFIX . '6',
            ],

        ];
    }

    /**
     * @dataProvider dataProviderTestAfterAddPageLayoutHandles
     */
    public function testExecuteUsesTheRightsHandles($footerConfig, $expectedFooterName, $headerConfig,
                                                    $expectedHeaderName, $productListitemConfig, $expectedProductListitemName, $bannerConfig, $expectedBannerName
    ) {
        $this
            ->scopeConfig
            ->expects($this->exactly(4))
            ->method('getValue')
            ->withConsecutive(
                [Subject::FOOTER_LAYOUT_CONFIG_PATH, ScopeInterface::SCOPE_STORE],
                [Subject::HEADER_LAYOUT_CONFIG_PATH, ScopeInterface::SCOPE_STORE],
                [Subject::PRODUCTLISTITEM_LAYOUT_CONFIG_PATH, ScopeInterface::SCOPE_STORE],
                [Subject::BANNER_LAYOUT_CONFIG_PATH, ScopeInterface::SCOPE_STORE]
            )->willReturnOnConsecutiveCalls(
                $footerConfig,
                $headerConfig,
                $productListitemConfig,
                $bannerConfig

            );

        $this
            ->request
            ->expects($this->once())
            ->method('getFullActionName')
            ->willReturn('cms_index_index');

        $this
            ->layout
            ->expects($this->once())
            ->method('getUpdate')
            ->willReturn($this->update);

        $this
            ->update
            ->expects($this->exactly(5))
            ->method('addHandle')
            ->withConsecutive(
                [$expectedFooterName],
                [$expectedHeaderName],
                [$expectedProductListitemName],
                [$expectedBannerName],
                [Subject::THEME_CUSOMIZATION_HANDLE_NAME]
            );

        $this
            ->observer
            ->expects($this->once())
            ->method('getData')
            ->with('layout')
            ->willReturn($this->layout);

        $this->subject->execute($this->observer);
    }

    public function testExecuteDoesntApplyHandlesWhenTheresNoLayout() {
        $this
            ->scopeConfig
            ->expects($this->never())
            ->method('getValue');

        $this
            ->request
            ->expects($this->never())
            ->method('getFullActionName');

        $this
            ->layout
            ->expects($this->never())
            ->method('getUpdate');

        $this
            ->observer
            ->expects($this->once())
            ->method('getData')
            ->with('layout')
            ->willReturn(null);

        $this->subject->execute($this->observer);
    }

    public function testExecuteDoesntApplyHandlesWhenTheresNoLayoutUpdate() {
        $this
            ->scopeConfig
            ->expects($this->never())
            ->method('getValue');

        $this
            ->request
            ->expects($this->never())
            ->method('getFullActionName');

        $this
            ->layout
            ->expects($this->once())
            ->method('getUpdate')
            ->willReturn(null);

        $this
            ->observer
            ->expects($this->once())
            ->method('getData')
            ->with('layout')
            ->willReturn($this->layout);

        $this->subject->execute($this->observer);
    }

    public function testExecuteDoesntApplyProductListHandlesWhenNotInProductListAction() {
        $this
            ->scopeConfig
            ->expects($this->any())
            ->method('getValue');

        $this
            ->request
            ->expects($this->once())
            ->method('getFullActionName')
            ->willReturn('not/a/productlist');

        $this
            ->layout
            ->expects($this->once())
            ->method('getUpdate')
            ->willReturn($this->update);

        $this
            ->update
            ->expects($this->any())
            ->method('addHandle')
            ->with($this->callback(function ($handle) {
                return !preg_match('/' . Subject::PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX . '/', $handle);
            }));

        $this
            ->observer
            ->expects($this->once())
            ->method('getData')
            ->with('layout')
            ->willReturn($this->layout);

        $this->subject->execute($this->observer);
    }

    public function testExecuteDoesntApplyBannerHandlesWhenNotInBannerHandletAction() {
        $this
            ->scopeConfig
            ->expects($this->any())
            ->method('getValue');

        $this
            ->request
            ->expects($this->once())
            ->method('getFullActionName')
            ->willReturn('not/a/banneraction');

        $this
            ->layout
            ->expects($this->once())
            ->method('getUpdate')
            ->willReturn($this->update);

        $this
            ->update
            ->expects($this->any())
            ->method('addHandle')
            ->with($this->callback(function ($handle) {
                return !preg_match('/' . Subject::BANNER_LAYOUT_HANDLE_PREFIX . '/', $handle);
            }));

        $this
            ->observer
            ->expects($this->once())
            ->method('getData')
            ->with('layout')
            ->willReturn($this->layout);

        $this->subject->execute($this->observer);
    }

    public function testExecuteUsesSearchHandleWhenOnSearchAction() {
        $this
            ->scopeConfig
            ->expects($this->any())
            ->method('getValue');

        $this
            ->request
            ->expects($this->once())
            ->method('getFullActionName')
            ->willReturn(Subject::PRODUCTSEARCHITEM_HANDLES[0]);

        $this
            ->layout
            ->expects($this->once())
            ->method('getUpdate')
            ->willReturn($this->update);

        $this
            ->update
            ->expects($this->at(2))
            ->method('addHandle')
            ->with($this->callback(function ($handle) {
                return preg_match('/' . Subject::PRODUCTSEARCHITEM_LAYOUT_HANDLE_PREFIX . '/', $handle);
            }));

        $this
            ->observer
            ->expects($this->once())
            ->method('getData')
            ->with('layout')
            ->willReturn($this->layout);

        $this->subject->execute($this->observer);
    }

    public function testExecuteUsesProductListHandleWhenOnProductListAction() {
        $this
            ->scopeConfig
            ->expects($this->any())
            ->method('getValue');

        $this
            ->request
            ->expects($this->once())
            ->method('getFullActionName')
            ->willReturn(Subject::PRODUCTLISTITEM_HANDLES[0]);

        $this
            ->layout
            ->expects($this->once())
            ->method('getUpdate')
            ->willReturn($this->update);

        $this
            ->update
            ->expects($this->at(2))
            ->method('addHandle')
            ->with($this->callback(function ($handle) {
                return preg_match('/' . Subject::PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX . '/', $handle);
            }));

        $this
            ->observer
            ->expects($this->once())
            ->method('getData')
            ->with('layout')
            ->willReturn($this->layout);

        $this->subject->execute($this->observer);
    }
}