<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Testing;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

class Listener implements TestListener
{
    public function addError(Test $test, \Exception $e, $time)
    {
        $this->error = true;
    }

    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->error = true;
    }

    public function addIncompleteTest(Test $test, \Exception $e, $time)
    {
    }

    public function addSkippedTest(Test $test, \Exception $e, $time)
    {
    }

    public function startTest(Test $test)
    {
    }

    public function endTest(Test $test, $time)
    {
    }

    public function startTestSuite(TestSuite $suite)
    {

    }

    public function endTestSuite(TestSuite $suite)
    {
        // If you have multiple test suites this is the wrong place to do anything
    }

    public function addWarning(Test $test, Warning $e, $time)
    {

    }

    public function addRiskyTest(Test $test, \Exception $e, $time)
    {

    }


    public function __destruct()
    {
        if ($this->error) {
            FileCreationTransaction::echoDirtyTransactionCleanupCommands();
        } else {
            FileCreationTransaction::markTransactionSuccessful();
        }
    }

}
