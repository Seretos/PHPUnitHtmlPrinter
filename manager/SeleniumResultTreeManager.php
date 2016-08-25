<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 20.08.2016
 * Time: 00:33
 */

namespace PHPUnitHtmlPrinter\manager;


use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;
use PHPUnitHtmlPrinter\interfaces\TestResultInterface;
use PHPUnitHtmlPrinter\manager\ResultTreeManager;
use PHPUnitHtmlPrinter\model\SeleniumTestResult;
use PHPUnitHtmlPrinter\model\TestResult;
use PHPUnitHtmlPrinter\model\TestSuiteResult;

class SeleniumResultTreeManager extends ResultTreeManager
{
    private static $SELENIUM_SUFFIX_ARR = ['firefox' => ': firefox', 'chrome' => ': chrome', 'iexplorer' => ': iexplorer'];

    private $currentBrowser;
    private $currentSuiteName;

    public function __construct()
    {
        parent::__construct();
    }

    public function addTest(PHPUnit_Framework_Test $test)
    {
        if ($this->currentBrowser == null) {
            parent::addTest($test);
            return;
        }
        $suite = $this->getLastOpenSuite();
        $testResult = $suite->findTestByName($test->getName());
        if ($testResult == null) {
            $testResult = new SeleniumTestResult($test->getName());
            $suite->addTest($testResult);
        }

        $browserTestResult = new TestResult($test);
        $testResult->addTest($browserTestResult, $this->currentBrowser);
        $this->currentTest = $browserTestResult;
    }

    public function addTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->currentBrowser = null;
        $this->currentSuiteName = null;
        foreach (self::$SELENIUM_SUFFIX_ARR as $browser => $search) {
            if (substr($suite->getName(), -strlen($search)) == $search) {
                $this->currentBrowser = $browser;
                $this->currentSuiteName = substr($suite->getName(), -strlen($search));
            }
        }

        if ($this->currentBrowser == null) {
            parent::addTestSuite($suite);
        }
    }

    public function findTestResult(PHPUnit_Framework_Test $test)
    {
        foreach ($this->getRootSuites() as $suite) {
            $testResult = $this->findTestResultBySuite($test, $suite);
            if ($testResult != null) {
                return $testResult;
            }
        }
        return null;
    }

    private function findTestResultBySuite(PHPUnit_Framework_Test $test, TestSuiteResult $suite)
    {
        foreach ($suite->getTests() as $testResult) {
            if ($testResult instanceof TestResult) {
                if ($testResult->getTest() == $test) {
                    return $testResult;
                }
            } else if ($testResult instanceof SeleniumTestResult) {
                foreach ($testResult->getTests() as $seleniumTest) {
                    if ($seleniumTest->getTest() == $test) {
                        return $seleniumTest;
                    }
                }
            }
        }

        foreach ($suite->getChildSuites() as $childSuite) {
            $testResult = $this->findTestResultBySuite($test, $childSuite);
            if ($testResult != null) {
                return $testResult;
            }
        }

        return null;
    }

    public function debug()
    {
        $this->printSuites($this->getRootSuites());
    }

    /**
     * @param TestSuiteResult[] $results
     * @param string $prefix
     */
    public function printSuites(array $results, $prefix = '')
    {
        foreach ($results as $result) {
            error_log($prefix . $result->getSuite()->getName());
            $this->printTests($result->getTests(), $prefix . '---');
            $this->printSuites($result->getChildSuites(), $prefix . '---');
        }
    }

    /**
     * @param TestResultInterface[] $tests
     * @param $prefix
     */
    private function printTests(array $tests, $prefix)
    {
        foreach ($tests as $test) {
            if ($test instanceof SeleniumTestResult) {
                error_log($prefix . $test->getName());
                foreach ($test->getTests() as $browser => $testResult) {
                    error_log('---' . $prefix . $browser . ': skipped=' . $testResult->isSkipped() . ' risky=' . $testResult->isRisky() . ' incomplete=' . $testResult->isIncomplete() . ' failure=' . $testResult->hasFailure() . ' error=' . $testResult->hasError());
                    if ($testResult->getException() != null) {
                        error_log('---' . $prefix . 'message: ' . $testResult->getException()->getMessage());
                    }
                    error_log('---' . $prefix . $testResult->getMessage());
                }
            } else {
                error_log($prefix . $test->getName() . ': skipped=' . $test->isSkipped() . ' risky=' . $test->isRisky() . ' incomplete=' . $test->isIncomplete() . ' failure=' . $test->hasFailure() . ' error=' . $test->hasError());
                if ($test->getException() != null) {
                    error_log('---' . $prefix . 'message: ' . $test->getException()->getMessage());
                }
                error_log('---' . $prefix . $test->getMessage());
            }
        }
    }
}