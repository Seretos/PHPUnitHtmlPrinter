<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.08.2016
 * Time: 20:57
 */

namespace PHPUnitHtmlPrinter\model;


use PHPUnit_Framework_TestSuite;
use PHPUnitHtmlPrinter\interfaces\TestResultInterface;

class TestSuiteResult
{
    /**
     * @var PHPUnit_Framework_TestSuite
     */
    private $suite;
    /**
     * @var TestSuiteResult[]
     */
    private $childSuites;

    /**
     * @var TestResultInterface[]
     */
    private $tests;

    /**
     * @var int
     */
    private $countSuccess;
    /**
     * @var int
     */
    private $countSkipped;
    /**
     * @var int
     */
    private $countRisky;
    /**
     * @var int
     */
    private $countIncomplete;
    /**
     * @var int
     */
    private $countFailed;
    /**
     * @var int
     */
    private $countError;

    public function __construct(PHPUnit_Framework_TestSuite $suite)
    {
        $this->suite = $suite;
        $this->childSuites = [];
        $this->tests = [];
        $this->countSuccess = 0;
        $this->countSkipped = 0;
        $this->countRisky = 0;
        $this->countIncomplete = 0;
        $this->countFailed = 0;
        $this->countError = 0;
    }

    public function calculate()
    {
        $this->calculateTests();
        $this->calculateSuites();
    }

    private function calculateSuites()
    {
        foreach ($this->childSuites as $suite) {
            $suite->calculate();
            $this->countSuccess += $suite->getCountSuccess();
            $this->countSkipped += $suite->getCountSkipped();
            $this->countRisky += $suite->getCountRisky();
            $this->countIncomplete += $suite->getCountIncomplete();
            $this->countFailed += $suite->getCountFailed();
            $this->countError += $suite->getCountError();
        }
    }

    private function calculateTests()
    {
        foreach ($this->tests as $test) {
            if ($test instanceof SeleniumTestResult) {
                foreach ($test->getTests() as $currentTest) {
                    $this->calculateTest($currentTest);
                }
            } else {
                $this->calculateTest($test);
            }
        }
    }

    private function calculateTest(TestResult $test)
    {
        if ($test->isSkipped()) {
            $this->countSkipped++;
        } else if ($test->isRisky()) {
            $this->countRisky++;
        } else if ($test->isIncomplete()) {
            $this->countIncomplete++;
        } else if ($test->hasError()) {
            $this->countError++;
        } else if ($test->hasFailure()) {
            $this->countFailed++;
        } else {
            $this->countSuccess++;
        }
    }

    public function addChildSuite(TestSuiteResult $suite)
    {
        $this->childSuites[] = $suite;
    }

    public function getChildSuites()
    {
        return $this->childSuites;
    }

    public function addTest(TestResultInterface $test)
    {
        $this->tests[] = $test;
    }

    public function findTestByName($name)
    {
        foreach ($this->tests as $test) {
            if ($test->getName() == $name) {
                return $test;
            }
        }
        return null;
    }

    public function getTests()
    {
        return $this->tests;
    }

    public function getSuite()
    {
        return $this->suite;
    }

    /**
     * @return int
     */
    public function getCountSuccess()
    {
        return $this->countSuccess;
    }

    /**
     * @return int
     */
    public function getCountSkipped()
    {
        return $this->countSkipped;
    }

    /**
     * @return int
     */
    public function getCountRisky()
    {
        return $this->countRisky;
    }

    /**
     * @return int
     */
    public function getCountIncomplete()
    {
        return $this->countIncomplete;
    }

    /**
     * @return int
     */
    public function getCountFailed()
    {
        return $this->countFailed;
    }

    /**
     * @return int
     */
    public function getCountError()
    {
        return $this->countError;
    }

    public function isSkipped()
    {
        return $this->countSkipped > 0;
    }

    public function isRisky()
    {
        return $this->countRisky > 0;
    }

    public function isIncomplete()
    {
        return $this->countIncomplete > 0;
    }

    public function hasFailure()
    {
        return $this->countFailed > 0;
    }

    public function hasError()
    {
        return $this->countError > 0;
    }
}