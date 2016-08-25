<?php
use PHPUnitHtmlPrinter\listener\TestResultListener;
use PHPUnitHtmlPrinter\listener\TestResultListenerFactory;
use PHPUnitHtmlPrinter\manager\SeleniumResultTreeManager;
use PHPUnitHtmlPrinter\model\TestResult;
use PHPUnitHtmlPrinter\model\TestSuiteResult;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 23.08.2016
 * Time: 20:48
 */
class TestResultListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|TestResultListenerFactory
     */
    private $factory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|SeleniumResultTreeManager
     */
    private $manager;

    /**
     * @var TestResultListener
     */
    private $listener;

    protected function setUp()
    {
        $this->factory = $this->getMockBuilder(TestResultListenerFactory::class)->disableOriginalConstructor()->getMock();
        $this->manager = $this->getMockBuilder(SeleniumResultTreeManager::class)->disableOriginalConstructor()->getMock();

        $this->factory->expects($this->once())->method('createManager')->will($this->returnValue($this->manager));

        $this->listener = new TestResultListener($this->factory, 'example.html', 'example');

        $reflectionClass = new ReflectionClass(TestResultListener::class);

        $factoryProperty = $reflectionClass->getProperty('factory');
        $factoryProperty->setAccessible(true);

        $filenameProperty = $reflectionClass->getProperty('filename');
        $filenameProperty->setAccessible(true);

        $managerProperty = $reflectionClass->getProperty('manager');
        $managerProperty->setAccessible(true);

        $this->assertSame($this->factory, $factoryProperty->getValue($this->listener));
        $this->assertSame($this->manager, $managerProperty->getValue($this->listener));
        $this->assertSame('example.html', $filenameProperty->getValue($this->listener));
    }

    protected function tearDown()
    {
        if (isset($this->listener)) {
            $this->buildDestructMethods();
        }
    }

    /**
     * @test
     */
    public function destruct_method()
    {
        $this->buildDestructMethods();
        $this->listener->__destruct();
    }

    /**
     * @test
     */
    public function addError_method()
    {
        $this->exceptionMethods('addError', 'setError');
    }

    /**
     * @test
     */
    public function addFailure_method()
    {
        $this->exceptionMethods('addFailure', 'setFailure', PHPUnit_Framework_AssertionFailedError::class);
    }

    /**
     * @test
     */
    public function addIncompleteTest_method()
    {
        $this->exceptionMethods('addIncompleteTest', 'setIncomplete');
    }

    /**
     * @test
     */
    public function addRiskyTest_method()
    {
        $this->exceptionMethods('addRiskyTest', 'setRisky');
    }

    /**
     * @test
     */
    public function addSkippedTest_method()
    {
        $this->exceptionMethods('addSkippedTest', 'setSkipped');
    }

    /**
     * @test
     */
    public function startTestSuite_method()
    {
        $mockSuite = $this->getMockBuilder(PHPUnit_Framework_TestSuite::class)->disableOriginalConstructor()->getMock();

        $this->manager->expects($this->once())->method('addTestSuite')->with($mockSuite);

        $this->listener->startTestSuite($mockSuite);
    }

    /**
     * @test
     */
    public function endTestSuite_method()
    {
        $mockSuite = $this->getMockBuilder(PHPUnit_Framework_TestSuite::class)->disableOriginalConstructor()->getMock();

        $this->manager->expects($this->once())->method('closeTestSuite')->with($mockSuite);

        $this->listener->endTestSuite($mockSuite);
    }

    /**
     * @test
     */
    public function startTest_method()
    {
        $mockTest = $this->getMockBuilder(PHPUnit_Framework_Test::class)->disableOriginalConstructor()->getMock();

        $this->manager->expects($this->once())->method('addTest')->with($mockTest);

        $this->listener->startTest($mockTest);
    }

    /**
     * @test
     */
    public function endTest_method()
    {
        $mockTest = $this->getMockBuilder(PHPUnit_Framework_Test::class)->disableOriginalConstructor()->getMock();

        $mockCurrentTest = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $this->manager->expects($this->once())->method('getCurrentTest')->will($this->returnValue($mockCurrentTest));

        $mockCurrentTest->expects($this->once())->method('setTime')->with(4);

        $this->listener->endTest($mockTest, 4);
    }

    private function buildDestructMethods()
    {
        $mockTwig = $this->getMockBuilder(Twig_Environment::class)->disableOriginalConstructor()->getMock();

        $mockSuite = $this->getMockBuilder(TestSuiteResult::class)->disableOriginalConstructor()->getMock();
        $mockUnitSuite = $this->getMockBuilder(PHPUnit_Framework_TestSuite::class)->disableOriginalConstructor()->getMock();

        $this->manager->expects($this->exactly(2))->method('getRootSuites')->will($this->returnValue([$mockSuite]));

        $mockSuite->expects($this->once())->method('calculate');
        $mockSuite->expects($this->exactly(2))->method('getSuite')->will($this->returnValue($mockUnitSuite));

        $mockUnitSuite->expects($this->once())->method('getName')->will($this->returnValue(''));
        $mockUnitSuite->expects($this->once())->method('setName')->with('example');

        $this->factory->expects($this->once())->method('createTwig')->will($this->returnValue($mockTwig));

        $mockTwig->expects($this->once())->method('render')->with('index.html.twig', ['root' => [$mockSuite]])->will($this->returnValue('content'));

        $this->factory->expects($this->once())->method('saveContent')->with('example.html', 'content');
    }

    private function exceptionMethods($method, $setMethod, $paramClass = Exception::class)
    {
        $mockTest = $this->getMockBuilder(PHPUnit_Framework_Test::class)->disableOriginalConstructor()->getMock();
        $mockException = $this->getMockBuilder($paramClass)->disableOriginalConstructor()->getMock();

        $mockCurrentTest = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();

        $this->manager->expects($this->exactly(2))->method('getCurrentTest')->will($this->returnValue($mockCurrentTest));

        $mockCurrentTest->expects($this->once())->method($setMethod);
        $mockCurrentTest->expects($this->once())->method('setException')->with($mockException);

        //$this->listener->addError($mockTest, $mockException, 3);

        $reflectionClass = new ReflectionClass(TestResultListener::class);
        $reflectionClass->getMethod($method)->invokeArgs($this->listener, [$mockTest, $mockException, 3]);
    }
}