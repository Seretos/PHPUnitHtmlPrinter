<?php
//namespace PHPUnitHtmlPrinter\listener {
//    function file_put_contents($filename, $content)
//    {
//        global $mockFilename;
//        global $mockContent;
//        $mockFilename = $filename;
//        $mockContent = $content;
//    }
//}
//
//namespace {

use PHPUnitHtmlPrinter\listener\TestResultListenerFactory;
use PHPUnitHtmlPrinter\manager\SeleniumResultTreeManager;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 23.08.2016
 * Time: 22:57
 */
class TestResultListenerFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TestResultListenerFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new TestResultListenerFactory();
        global $mockFilename;
        global $mockContent;
        $mockFilename = '';
        $mockContent = '';
    }

    /**
     * @test
     */
    public function createManager()
    {
        $this->assertInstanceOf(SeleniumResultTreeManager::class, $this->factory->createManager());
    }

    /**
     * @test
     */
    public function createTwig()
    {
        $this->assertInstanceOf(Twig_Environment::class, $this->factory->createTwig());
    }

//        /**
//         * @test
//         */
//        public function saveContent()
//        {
//            $this->factory->saveContent('test.html', 'test content');
//            global $mockFilename;
//            global $mockContent;
//
//            $this->assertSame('test.html', $mockFilename);
//            $this->assertSame('test content', $mockContent);
//        }
}
//}