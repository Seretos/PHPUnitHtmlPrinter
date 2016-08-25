<?php
use PHPUnitHtmlPrinter\model\SeleniumTestResult;
use PHPUnitHtmlPrinter\model\TestResult;
use PHPUnitHtmlPrinter\model\TestSuiteResult;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 22.08.2016
 * Time: 23:14
 */
class TestSuiteResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TestSuiteResult
     */
    private $result;

    /**
     * @var PHPUnit_Framework_TestSuite|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockTestSuite;

    protected function setUp()
    {
        $this->mockTestSuite = $this->getMockBuilder(PHPUnit_Framework_TestSuite::class)->disableOriginalConstructor()->getMock();
        $this->result = new TestSuiteResult($this->mockTestSuite);

        $this->assertSame($this->mockTestSuite, $this->result->getSuite());

        $this->assertSame([], $this->result->getChildSuites());
        $this->assertSame([], $this->result->getTests());
        $this->assertSame(0, $this->result->getCountSuccess());
        $this->assertSame(0, $this->result->getCountSkipped());
        $this->assertSame(0, $this->result->getCountRisky());
        $this->assertSame(0, $this->result->getCountIncomplete());
        $this->assertSame(0, $this->result->getCountFailed());
        $this->assertSame(0, $this->result->getCountError());
    }

    /**
     * @test
     */
    public function findTestByName_withoutTests()
    {
        $mockTest = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockTest->expects($this->once())->method('getName')->will($this->returnValue('test'));

        $this->assertSame(null, $this->result->findTestByName('example'));

        $this->result->addTest($mockTest);
        $this->assertSame(null, $this->result->findTestByName('example'));
    }

    /**
     * @test
     */
    public function findTestByName()
    {
        $mockTest = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockTest->expects($this->once())->method('getName')->will($this->returnValue('test'));
        $mockTest2 = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockTest2->expects($this->once())->method('getName')->will($this->returnValue('example'));

        $this->result->addTest($mockTest);
        $this->result->addTest($mockTest2);
        $this->assertSame($mockTest2, $this->result->findTestByName('example'));
    }

    /**
     * @test
     */
    public function calculate_withMultipleSuites()
    {
        $mockSuite1 = $this->getMockBuilder(TestSuiteResult::class)->disableOriginalConstructor()->getMock();
        $mockSuite2 = $this->getMockBuilder(TestSuiteResult::class)->disableOriginalConstructor()->getMock();

        $mockSuite1->expects($this->at(0))->method('calculate');
        $mockSuite1->expects($this->at(1))->method('getCountSuccess')->will($this->returnValue(1));
        $mockSuite1->expects($this->at(2))->method('getCountSkipped')->will($this->returnValue(2));
        $mockSuite1->expects($this->at(3))->method('getCountRisky')->will($this->returnValue(3));
        $mockSuite1->expects($this->at(4))->method('getCountIncomplete')->will($this->returnValue(4));
        $mockSuite1->expects($this->at(5))->method('getCountFailed')->will($this->returnValue(5));
        $mockSuite1->expects($this->at(6))->method('getCountError')->will($this->returnValue(6));

        $mockSuite2->expects($this->at(0))->method('calculate');
        $mockSuite2->expects($this->at(1))->method('getCountSuccess')->will($this->returnValue(2));
        $mockSuite2->expects($this->at(2))->method('getCountSkipped')->will($this->returnValue(3));
        $mockSuite2->expects($this->at(3))->method('getCountRisky')->will($this->returnValue(4));
        $mockSuite2->expects($this->at(4))->method('getCountIncomplete')->will($this->returnValue(5));
        $mockSuite2->expects($this->at(5))->method('getCountFailed')->will($this->returnValue(6));
        $mockSuite2->expects($this->at(6))->method('getCountError')->will($this->returnValue(7));

        $this->result->addChildSuite($mockSuite1);
        $this->result->addChildSuite($mockSuite2);

        $this->result->calculate();

        $this->assertSame(3, $this->result->getCountSuccess());
        $this->assertSame(5, $this->result->getCountSkipped());
        $this->assertSame(7, $this->result->getCountRisky());
        $this->assertSame(9, $this->result->getCountIncomplete());
        $this->assertSame(11, $this->result->getCountFailed());
        $this->assertSame(13, $this->result->getCountError());
    }

    /**
     * @test
     */
    public function calculate_withMultipleTests()
    {
        $mockTest1 = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockTest2 = $this->getMockBuilder(SeleniumTestResult::class)->disableOriginalConstructor()->getMock();

        $mockSeleniumTest1 = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockSeleniumTest2 = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();

        $this->result->addTest($mockTest1);
        $this->result->addTest($mockTest2);

        $mockTest1->expects($this->once())->method('isSkipped')->will($this->returnValue(true));
        $mockTest2->expects($this->once())->method('getTests')->will($this->returnValue([$mockSeleniumTest1, $mockSeleniumTest2]));

        $mockSeleniumTest1->expects($this->once())->method('isSkipped')->will($this->returnValue(false));
        $mockSeleniumTest1->expects($this->once())->method('isRisky')->will($this->returnValue(true));

        $mockSeleniumTest2->expects($this->once())->method('isSkipped')->will($this->returnValue(false));
        $mockSeleniumTest2->expects($this->once())->method('isRisky')->will($this->returnValue(false));
        $mockSeleniumTest2->expects($this->once())->method('isIncomplete')->will($this->returnValue(true));

        $this->result->calculate();

        $this->assertSame(0, $this->result->getCountSuccess());
        $this->assertSame(1, $this->result->getCountSkipped());
        $this->assertSame(1, $this->result->getCountRisky());
        $this->assertSame(1, $this->result->getCountIncomplete());
        $this->assertSame(0, $this->result->getCountFailed());
        $this->assertSame(0, $this->result->getCountError());
    }

    /**
     * @test
     */
    public function calculateTest_withSkippedTest()
    {
        $mockResult = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockResult->expects($this->once())->method('isSkipped')->will($this->returnValue(true));

        $this->assertSame(false, $this->result->isSkipped());
        $this->callCalculateTest($mockResult);

        $this->assertSame(true, $this->result->isSkipped());
        $this->assertSame(0, $this->result->getCountSuccess());
        $this->assertSame(1, $this->result->getCountSkipped());
        $this->assertSame(0, $this->result->getCountRisky());
        $this->assertSame(0, $this->result->getCountIncomplete());
        $this->assertSame(0, $this->result->getCountFailed());
        $this->assertSame(0, $this->result->getCountError());
    }

    /**
     * @test
     */
    public function calculateTest_withRiskyTest()
    {
        $mockResult = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockResult->expects($this->once())->method('isSkipped')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('isRisky')->will($this->returnValue(true));

        $this->assertSame(false, $this->result->isRisky());
        $this->callCalculateTest($mockResult);

        $this->assertSame(true, $this->result->isRisky());
        $this->assertSame(0, $this->result->getCountSuccess());
        $this->assertSame(0, $this->result->getCountSkipped());
        $this->assertSame(1, $this->result->getCountRisky());
        $this->assertSame(0, $this->result->getCountIncomplete());
        $this->assertSame(0, $this->result->getCountFailed());
        $this->assertSame(0, $this->result->getCountError());
    }

    /**
     * @test
     */
    public function calculateTest_withIncompleteTest()
    {
        $mockResult = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockResult->expects($this->once())->method('isSkipped')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('isRisky')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('isIncomplete')->will($this->returnValue(true));

        $this->assertSame(false, $this->result->isIncomplete());
        $this->callCalculateTest($mockResult);

        $this->assertSame(true, $this->result->isIncomplete());
        $this->assertSame(0, $this->result->getCountSuccess());
        $this->assertSame(0, $this->result->getCountSkipped());
        $this->assertSame(0, $this->result->getCountRisky());
        $this->assertSame(1, $this->result->getCountIncomplete());
        $this->assertSame(0, $this->result->getCountFailed());
        $this->assertSame(0, $this->result->getCountError());
    }

    /**
     * @test
     */
    public function calculateTest_withErrorTest()
    {
        $mockResult = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockResult->expects($this->once())->method('isSkipped')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('isRisky')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('isIncomplete')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('hasError')->will($this->returnValue(true));

        $this->assertSame(false, $this->result->hasError());
        $this->callCalculateTest($mockResult);

        $this->assertSame(true, $this->result->hasError());
        $this->assertSame(0, $this->result->getCountSuccess());
        $this->assertSame(0, $this->result->getCountSkipped());
        $this->assertSame(0, $this->result->getCountRisky());
        $this->assertSame(0, $this->result->getCountIncomplete());
        $this->assertSame(0, $this->result->getCountFailed());
        $this->assertSame(1, $this->result->getCountError());
    }

    /**
     * @test
     */
    public function calculateTest_withFailureTest()
    {
        $mockResult = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockResult->expects($this->once())->method('isSkipped')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('isRisky')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('isIncomplete')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('hasError')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('hasFailure')->will($this->returnValue(true));

        $this->assertSame(false, $this->result->hasFailure());
        $this->callCalculateTest($mockResult);

        $this->assertSame(true, $this->result->hasFailure());
        $this->assertSame(0, $this->result->getCountSuccess());
        $this->assertSame(0, $this->result->getCountSkipped());
        $this->assertSame(0, $this->result->getCountRisky());
        $this->assertSame(0, $this->result->getCountIncomplete());
        $this->assertSame(1, $this->result->getCountFailed());
        $this->assertSame(0, $this->result->getCountError());
    }

    /**
     * @test
     */
    public function calculateTest_withSuccessTest()
    {
        $mockResult = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockResult->expects($this->once())->method('isSkipped')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('isRisky')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('isIncomplete')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('hasError')->will($this->returnValue(false));
        $mockResult->expects($this->once())->method('hasFailure')->will($this->returnValue(false));

        $this->callCalculateTest($mockResult);

        $this->assertSame(1, $this->result->getCountSuccess());
        $this->assertSame(0, $this->result->getCountSkipped());
        $this->assertSame(0, $this->result->getCountRisky());
        $this->assertSame(0, $this->result->getCountIncomplete());
        $this->assertSame(0, $this->result->getCountFailed());
        $this->assertSame(0, $this->result->getCountError());
    }

    private function callCalculateTest(PHPUnit_Framework_MockObject_MockObject $mockResult)
    {
        $reflectionClass = new ReflectionClass(TestSuiteResult::class);
        $calculateMethod = $reflectionClass->getMethod('calculateTest');
        $calculateMethod->setAccessible(true);

        $calculateMethod->invokeArgs($this->result, [$mockResult]);
    }
}