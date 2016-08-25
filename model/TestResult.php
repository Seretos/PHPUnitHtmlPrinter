<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.08.2016
 * Time: 20:58
 */

namespace PHPUnitHtmlPrinter\model;


use PHPUnit_Framework_Test;
use PHPUnitHtmlPrinter\interfaces\TestResultInterface;

class TestResult implements TestResultInterface
{
    /**
     * @var PHPUnit_Framework_Test
     */
    private $test;

    /**
     * @var boolean
     */
    private $skipped;

    /**
     * @var boolean
     */
    private $risky;

    /**
     * @var boolean
     */
    private $incomplete;

    /**
     * @var boolean
     */
    private $failure;

    /**
     * @var boolean
     */
    private $error;

    /**
     * @var float
     */
    private $time;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var string
     */
    private $message;

    public function __construct(PHPUnit_Framework_Test $test)
    {
        $this->test = $test;
        $this->skipped = false;
        $this->risky = false;
        $this->incomplete = false;
        $this->failure = false;
        $this->error = false;
        $this->time = 0;
        $this->exception = null;
        $this->message = '';
    }

    public function getTest()
    {
        return $this->test;
    }

    public function getName()
    {
        return $this->test->getName();
    }

    public function setSkipped($skipped = true)
    {
        $this->skipped = $skipped;
    }

    public function setRisky($risky = true)
    {
        $this->risky = $risky;
    }

    public function setIncomplete($incomplete = true)
    {
        $this->incomplete = $incomplete;
    }

    public function setFailure($failure = true)
    {
        $this->failure = $failure;
    }

    public function setError($error = true)
    {
        $this->error = $error;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }

    public function isSkipped()
    {
        return $this->skipped;
    }

    public function isRisky()
    {
        return $this->risky;
    }

    public function isIncomplete()
    {
        return $this->incomplete;
    }

    public function hasFailure()
    {
        return $this->failure;
    }

    public function hasError()
    {
        return $this->error;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}