<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\Connection;

class ShouldConnectionByRetried
{
    public const KEY_USE_RECONNECT = 'should-reconnect';
    public const KEY_RECONNECT_ATTEMPTS = 'reconnect-attempts';
    public const KEY_RECONNECT_TIMEOUT = 'reconnect-timeout';
    /**
     * @var int
     */
    private $allowedAttempts;
    /**
     * @var int
     */
    private $timeout;

    private function __construct(int $allowedAttempts, int $timeout)
    {
        $this->allowedAttempts = $allowedAttempts;
        $this->timeout         = $timeout;
    }

    public static function createWithConfigParams(array $config): ShouldConnectionByRetried
    {
        $allowedAttempts = $config['driverOptions'][self::KEY_RECONNECT_ATTEMPTS] ?? 3;
        $timeout         = $config['driverOptions'][self::KEY_RECONNECT_TIMEOUT] ?? 5;

        return new self($allowedAttempts, $timeout);
    }

    public function checkAndSleep(
        \Exception $exception,
        int $transactionNestingLevel,
        int $numberOfAttempts,
        bool $ignoreTransactionLevel = false
    ): bool {
        switch (true) {
            case $this->attemptsHaveExceededLimit($numberOfAttempts):
            case $this->transactionLevelWillCauseProblems($ignoreTransactionLevel, $transactionNestingLevel):
            case $this->exceptionIsNotRelatedToLostConnection($exception):
                $retry = false;
                break;
            default:
                sleep($this->timeout);
                $retry = true;
        }

        return $retry;
    }


    private function attemptsHaveExceededLimit(int $numberOfAttempts): bool
    {
        return $numberOfAttempts > $this->allowedAttempts;
    }

    private function exceptionIsNotRelatedToLostConnection(\Exception $exception): bool
    {
        $message = $exception->getMessage();
        switch (true) {
            case \ts\stringContains($message, 'MySQL server has gone away'):
            case \ts\stringContains($message, 'Lost connection to MySQL server during query'):
            case \ts\stringContains($message, 'Connection timed out'):
                return false;
            default:
                return true;
        }
    }


    private function transactionLevelWillCauseProblems(bool $ignoreTransactionLevel, int $transactionNestingLevel): bool
    {
        if ($ignoreTransactionLevel === true) {
            return false;
        }

        return $transactionNestingLevel === 0;
    }
}
