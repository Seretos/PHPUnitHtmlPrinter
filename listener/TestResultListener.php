<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.08.2016
 * Time: 20:07
 */

namespace PHPUnitHtmlPrinter\listener;


use Exception;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;
use PHPUnitHtmlPrinter\manager\ResultTreeManager;

class TestResultListener implements \PHPUnit_Framework_TestListener
{
    /**
     * @var ResultTreeManager
     */
    private $manager;

    private $filename;
    /**
     * @var TestResultListenerFactory
     */
    private $factory;
    /**
     * @var string
     */
    private $rootName;

    public function __construct(TestResultListenerFactory $factory, $filename, $name = '')
    {
        $this->factory = $factory;
        $this->manager = $factory->createManager();
        $this->filename = $filename;
        $this->rootName = $name;
    }

    public function __destruct()
    {
        $twig = $this->factory->createTwig();
        foreach ($this->manager->getRootSuites() as $suite) {
            $suite->calculate();
            if ($suite->getSuite()->getName() == '') {
                $suite->getSuite()->setName($this->rootName);
            }
        }
        $this->factory->saveContent($this->filename, $twig->render('index.html.twig', array('root' => $this->manager->getRootSuites())));
    }

    /**
     * An error occurred.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->manager->getCurrentTest()->setError();
        $this->manager->getCurrentTest()->setException($e);
//        parent::addError($test, $e, $time);
    }

    /**
     * A failure occurred.
     *
     * @param PHPUnit_Framework_Test $test
     * @param PHPUnit_Framework_AssertionFailedError $e
     * @param float $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->manager->getCurrentTest()->setFailure();
        $this->manager->getCurrentTest()->setException($e);
//        parent::addFailure($test, $e, $time);
    }

    /**
     * Incomplete test.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->manager->getCurrentTest()->setIncomplete();
        $this->manager->getCurrentTest()->setException($e);
//        parent::addIncompleteTest($test, $e, $time);
    }

    /**
     * Risky test.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     *
     * @since Method available since Release 4.0.0
     */
    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->manager->getCurrentTest()->setRisky();
        $this->manager->getCurrentTest()->setException($e);
//        parent::addRiskyTest($test, $e, $time);
    }

    /**
     * Skipped test.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     *
     * @since Method available since Release 3.0.0
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->manager->getCurrentTest()->setSkipped();
        $this->manager->getCurrentTest()->setException($e);
//        parent::addSkippedTest($test, $e, $time);
    }

    /**
     * A test suite started.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     *
     * @since Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->manager->addTestSuite($suite);
//        parent::startTestSuite($suite);
    }

    /**
     * A test suite ended.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     *
     * @since Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->manager->closeTestSuite($suite);
//        parent::endTestSuite($suite);
    }

    /**
     * A test started.
     *
     * @param PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        $this->manager->addTest($test);
//        parent::startTest($test);
    }

    /**
     * A test ended.
     *
     * @param PHPUnit_Framework_Test $test
     * @param float $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $this->manager->getCurrentTest()->setTime($time);
//        parent::endTest($test, $time);
    }
}