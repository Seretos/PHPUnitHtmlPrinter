<?php
use PHPUnitHtmlPrinter\model\SeleniumTestResult;
use PHPUnitHtmlPrinter\model\TestResult;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 22.08.2016
 * Time: 22:54
 */
class SeleniumTestResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SeleniumTestResult
     */
    private $result;

    protected function setUp()
    {
        $this->result = new SeleniumTestResult('exampleResult');
        $this->assertSame('exampleResult', $this->result->getName());
        $this->assertSame([], $this->result->getTests());
    }

    /**
     * @test
     */
    public function addTest()
    {
        $mockTest1 = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockTest2 = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockTest3 = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockTest4 = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();

        $this->result->addTest($mockTest1, 'firefox');
        $this->result->addTest($mockTest2, 'chrome');
        $this->result->addTest($mockTest3, 'iexplorer');

        $this->assertSame(['firefox' => $mockTest1, 'chrome' => $mockTest2, 'iexplorer' => $mockTest3], $this->result->getTests());

        $this->result->addTest($mockTest4, 'firefox');

        $this->assertSame(['firefox' => $mockTest4, 'chrome' => $mockTest2, 'iexplorer' => $mockTest3], $this->result->getTests());
    }

    /**
     * @test
     */
    public function hasError()
    {
        $mockTest1 = $this->createTestMock_stateMethod('hasError');
        $mockTest2 = $this->createTestMock_stateMethod('hasError');
        $mockTest3 = $this->createTestMock_stateMethod('hasError');
        $mockTest4 = $this->createTestMock_stateMethod('hasError', true);

        $this->result->addTest($mockTest1, 'firefox');
        $this->result->addTest($mockTest2, 'chrome');
        $this->result->addTest($mockTest3, 'iexplorer');

        $this->assertSame(false, $this->result->hasError());

        $this->result->addTest($mockTest4, 'firefox');

        $this->assertSame(true, $this->result->hasError());
    }

    /**
     * @test
     */
    public function hasFailure()
    {
        $mockTest1 = $this->createTestMock_stateMethod('hasFailure');
        $mockTest2 = $this->createTestMock_stateMethod('hasFailure');
        $mockTest3 = $this->createTestMock_stateMethod('hasFailure');
        $mockTest4 = $this->createTestMock_stateMethod('hasFailure', true);

        $this->result->addTest($mockTest1, 'firefox');
        $this->result->addTest($mockTest2, 'chrome');
        $this->result->addTest($mockTest3, 'iexplorer');

        $this->assertSame(false, $this->result->hasFailure());

        $this->result->addTest($mockTest4, 'firefox');

        $this->assertSame(true, $this->result->hasFailure());
    }

    /**
     * @test
     */
    public function isSkipped()
    {
        $mockTest1 = $this->createTestMock_stateMethod('isSkipped');
        $mockTest2 = $this->createTestMock_stateMethod('isSkipped');
        $mockTest3 = $this->createTestMock_stateMethod('isSkipped');
        $mockTest4 = $this->createTestMock_stateMethod('isSkipped', true);

        $this->result->addTest($mockTest1, 'firefox');
        $this->result->addTest($mockTest2, 'chrome');
        $this->result->addTest($mockTest3, 'iexplorer');

        $this->assertSame(false, $this->result->isSkipped());

        $this->result->addTest($mockTest4, 'firefox');

        $this->assertSame(true, $this->result->isSkipped());
    }

    /**
     * @test
     */
    public function isIncomplete()
    {
        $mockTest1 = $this->createTestMock_stateMethod('isIncomplete');
        $mockTest2 = $this->createTestMock_stateMethod('isIncomplete');
        $mockTest3 = $this->createTestMock_stateMethod('isIncomplete');
        $mockTest4 = $this->createTestMock_stateMethod('isIncomplete', true);

        $this->result->addTest($mockTest1, 'firefox');
        $this->result->addTest($mockTest2, 'chrome');
        $this->result->addTest($mockTest3, 'iexplorer');

        $this->assertSame(false, $this->result->isIncomplete());

        $this->result->addTest($mockTest4, 'firefox');

        $this->assertSame(true, $this->result->isIncomplete());
    }

    /**
     * @test
     */
    public function isRisky()
    {
        $mockTest1 = $this->createTestMock_stateMethod('isRisky');
        $mockTest2 = $this->createTestMock_stateMethod('isRisky');
        $mockTest3 = $this->createTestMock_stateMethod('isRisky');
        $mockTest4 = $this->createTestMock_stateMethod('isRisky', true);

        $this->result->addTest($mockTest1, 'firefox');
        $this->result->addTest($mockTest2, 'chrome');
        $this->result->addTest($mockTest3, 'iexplorer');

        $this->assertSame(false, $this->result->isRisky());

        $this->result->addTest($mockTest4, 'firefox');

        $this->assertSame(true, $this->result->isRisky());
    }

    private function createTestMock_stateMethod($method, $hasError = false)
    {
        $mockTest = $this->getMockBuilder(TestResult::class)->disableOriginalConstructor()->getMock();
        $mockTest->expects($this->once())->method($method)->will($this->returnValue($hasError));

        return $mockTest;
    }
}