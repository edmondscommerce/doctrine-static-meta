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
    private $error = false;

    public function addError(Test $test, \Throwable $e, $time)
    {
        $this->error = true;
    }

    public function addWarning(Test $test, Warning $e, $time)
    {
        // do nothing
    }

    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->error = true;
    }

    public function addIncompleteTest(Test $test, \Throwable $e, $time)
    {
        // do nothing
    }

    public function addRiskyTest(Test $test, \Throwable $e, $time)
    {
        // do nothing
    }

    public function addSkippedTest(Test $test, \Throwable $e, $time)
    {
        // do nothing
    }

    public function startTestSuite(TestSuite $suite)
    {
        // do nothing
    }

    public function endTestSuite(TestSuite $suite)
    {
        // do nothing
    }

    public function startTest(Test $test)
    {
        // do nothing
    }

    public function endTest(Test $test, $time)
    {
        // do nothing
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
