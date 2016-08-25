<?php
use PHPUnitHtmlPrinter\model\TestResult;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 22.08.2016
 * Time: 22:03
 */
class TestResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TestResult
     */
    private $result;

    /**
     * @var PHPUnit_Framework_Test|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockTest;

    protected function setUp()
    {
        $this->mockTest = $this->getMockBuilder(PHPUnit_Framework_TestCase::class)->getMock();
        $this->result = new TestResult($this->mockTest);

        $this->assertSame($this->mockTest, $this->result->getTest());
        $this->assertSame(false, $this->result->isSkipped());
        $this->assertSame(false, $this->result->isRisky());
        $this->assertSame(false, $this->result->isIncomplete());
        $this->assertSame(false, $this->result->hasFailure());
        $this->assertSame(false, $this->result->hasError());
        $this->assertSame(0, $this->result->getTime());
        $this->assertSame(null, $this->result->getException());
        $this->assertSame('', $this->result->getMessage());
    }

    /**
     * @test
     */
    public function getName_Method()
    {
        $this->mockTest->expects($this->once())->method('getName')->will($this->returnValue('test'));

        $this->assertSame('test', $this->result->getName());
    }

    /**
     * @test
     */
    public function setSkipped()
    {
        $this->result->setSkipped();

        $this->assertSame($this->mockTest, $this->result->getTest());
        $this->assertSame(true, $this->result->isSkipped());
        $this->assertSame(false, $this->result->isRisky());
        $this->assertSame(false, $this->result->isIncomplete());
        $this->assertSame(false, $this->result->hasFailure());
        $this->assertSame(false, $this->result->hasError());
        $this->assertSame(0, $this->result->getTime());
        $this->assertSame(null, $this->result->getException());
        $this->assertSame('', $this->result->getMessage());

        $this->result->setSkipped(false);
        $this->assertSame(false, $this->result->isSkipped());
    }

    /**
     * @test
     */
    public function setRisky()
    {
        $this->result->setRisky();

        $this->assertSame($this->mockTest, $this->result->getTest());
        $this->assertSame(false, $this->result->isSkipped());
        $this->assertSame(true, $this->result->isRisky());
        $this->assertSame(false, $this->result->isIncomplete());
        $this->assertSame(false, $this->result->hasFailure());
        $this->assertSame(false, $this->result->hasError());
        $this->assertSame(0, $this->result->getTime());
        $this->assertSame(null, $this->result->getException());
        $this->assertSame('', $this->result->getMessage());

        $this->result->setRisky(false);
        $this->assertSame(false, $this->result->isRisky());
    }

    /**
     * @test
     */
    public function setIncomplete()
    {
        $this->result->setIncomplete();

        $this->assertSame($this->mockTest, $this->result->getTest());
        $this->assertSame(false, $this->result->isSkipped());
        $this->assertSame(false, $this->result->isRisky());
        $this->assertSame(true, $this->result->isIncomplete());
        $this->assertSame(false, $this->result->hasFailure());
        $this->assertSame(false, $this->result->hasError());
        $this->assertSame(0, $this->result->getTime());
        $this->assertSame(null, $this->result->getException());
        $this->assertSame('', $this->result->getMessage());

        $this->result->setIncomplete(false);
        $this->assertSame(false, $this->result->isIncomplete());
    }

    /**
     * @test
     */
    public function setFailure()
    {
        $this->result->setFailure();

        $this->assertSame($this->mockTest, $this->result->getTest());
        $this->assertSame(false, $this->result->isSkipped());
        $this->assertSame(false, $this->result->isRisky());
        $this->assertSame(false, $this->result->isIncomplete());
        $this->assertSame(true, $this->result->hasFailure());
        $this->assertSame(false, $this->result->hasError());
        $this->assertSame(0, $this->result->getTime());
        $this->assertSame(null, $this->result->getException());
        $this->assertSame('', $this->result->getMessage());

        $this->result->setFailure(false);
        $this->assertSame(false, $this->result->hasFailure());
    }

    /**
     * @test
     */
    public function setError()
    {
        $this->result->setError();

        $this->assertSame($this->mockTest, $this->result->getTest());
        $this->assertSame(false, $this->result->isSkipped());
        $this->assertSame(false, $this->result->isRisky());
        $this->assertSame(false, $this->result->isIncomplete());
        $this->assertSame(false, $this->result->hasFailure());
        $this->assertSame(true, $this->result->hasError());
        $this->assertSame(0, $this->result->getTime());
        $this->assertSame(null, $this->result->getException());
        $this->assertSame('', $this->result->getMessage());

        $this->result->setError(false);
        $this->assertSame(false, $this->result->hasError());
    }

    /**
     * @test
     */
    public function setTime()
    {
        $this->result->setTime(14.35);

        $this->assertSame($this->mockTest, $this->result->getTest());
        $this->assertSame(false, $this->result->isSkipped());
        $this->assertSame(false, $this->result->isRisky());
        $this->assertSame(false, $this->result->isIncomplete());
        $this->assertSame(false, $this->result->hasFailure());
        $this->assertSame(false, $this->result->hasError());
        $this->assertSame(14.35, $this->result->getTime());
        $this->assertSame(null, $this->result->getException());
        $this->assertSame('', $this->result->getMessage());
    }

    /**
     * @test
     */
    public function setException()
    {
        $mockException = $this->getMockBuilder(Exception::class)->disableOriginalConstructor()->getMock();
        $this->result->setException($mockException);

        $this->assertSame($this->mockTest, $this->result->getTest());
        $this->assertSame(false, $this->result->isSkipped());
        $this->assertSame(false, $this->result->isRisky());
        $this->assertSame(false, $this->result->isIncomplete());
        $this->assertSame(false, $this->result->hasFailure());
        $this->assertSame(false, $this->result->hasError());
        $this->assertSame(0, $this->result->getTime());
        $this->assertSame($mockException, $this->result->getException());
        $this->assertSame('', $this->result->getMessage());
    }

    /**
     * @test
     */
    public function setMessage()
    {
        $this->result->setMessage('test message');

        $this->assertSame($this->mockTest, $this->result->getTest());
        $this->assertSame(false, $this->result->isSkipped());
        $this->assertSame(false, $this->result->isRisky());
        $this->assertSame(false, $this->result->isIncomplete());
        $this->assertSame(false, $this->result->hasFailure());
        $this->assertSame(false, $this->result->hasError());
        $this->assertSame(0, $this->result->getTime());
        $this->assertSame(null, $this->result->getException());
        $this->assertSame('test message', $this->result->getMessage());
    }
}