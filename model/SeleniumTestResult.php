<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.08.2016
 * Time: 21:00
 */

namespace PHPUnitHtmlPrinter\model;


use PHPUnitHtmlPrinter\interfaces\TestResultInterface;

class SeleniumTestResult implements TestResultInterface
{
    /**
     * @var TestResult[]
     */
    private $tests;
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
        $this->tests = [];
    }

    public function addTest(TestResult $test, $browser)
    {
        $this->tests[$browser] = $test;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTests()
    {
        return $this->tests;
    }

    public function hasError()
    {
        foreach ($this->tests as $test) {
            if ($test->hasError()) {
                return true;
            }
        }
        return false;
    }

    public function hasFailure()
    {
        foreach ($this->tests as $test) {
            if ($test->hasFailure()) {
                return true;
            }
        }
        return false;
    }

    public function isSkipped()
    {
        foreach ($this->tests as $test) {
            if ($test->isSkipped()) {
                return true;
            }
        }
        return false;
    }

    public function isIncomplete()
    {
        foreach ($this->tests as $test) {
            if ($test->isIncomplete()) {
                return true;
            }
        }
        return false;
    }

    public function isRisky()
    {
        foreach ($this->tests as $test) {
            if ($test->isRisky()) {
                return true;
            }
        }
        return false;
    }
}