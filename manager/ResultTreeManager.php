<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.08.2016
 * Time: 21:12
 */

namespace PHPUnitHtmlPrinter\manager;


use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;
use PHPUnitHtmlPrinter\model\TestResult;
use PHPUnitHtmlPrinter\model\TestSuiteResult;

class ResultTreeManager
{
    /**
     * @var TestSuiteResult[]
     */
    private $openSuites;
    /**
     * @var TestSuiteResult[]
     */
    private $rootSuites;

    /**
     * @var TestResult
     */
    protected $currentTest;

    public function __construct()
    {
        $this->openSuites = [];
        $this->rootSuites = [];
    }

    public function addTest(PHPUnit_Framework_Test $test)
    {
        if (count($this->openSuites) == 0) {
            throw new \Exception('tests require a parent suite for this printer!');
        }

        $testResult = new TestResult($test);
        $this->openSuites[count($this->openSuites) - 1]->addTest($testResult);
        $this->currentTest = $testResult;
    }

    public function getCurrentTest()
    {
        return $this->currentTest;
    }

    public function addTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $suiteResult = new TestSuiteResult($suite);

        if (count($this->openSuites) == 0) {
            $this->rootSuites[] = $suiteResult;
        } else {
            $this->openSuites[count($this->openSuites) - 1]->addChildSuite($suiteResult);
        }
        $this->openSuites[] = $suiteResult;
    }

    public function closeTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        foreach ($this->openSuites as &$currentSuite) {
            if ($currentSuite->getSuite() == $suite) {
                $currentSuite = null;
                break;
            }
        }
        $this->openSuites = array_filter($this->openSuites);
    }

    protected function getLastOpenSuite()
    {
        return $this->openSuites[count($this->openSuites) - 1];
    }

    public function getRootSuites()
    {
        return $this->rootSuites;
    }
}