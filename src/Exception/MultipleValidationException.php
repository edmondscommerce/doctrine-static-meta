<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception;

use Exception;

class MultipleValidationException extends ValidationException
{
    /**
     * @param ValidationException[] $validationExceptions
     * @param int                   $code
     * @param Exception|null       $previous
     */
    public function __construct(array $validationExceptions, int $code = 0, ?Exception $previous = null)
    {
        $message = count($validationExceptions) . ' validation exceptions: ';
        foreach ($validationExceptions as $validationException) {
            $message .= "\n\n" . $validationException->getMessage();
        }

        parent::__construct($message, $code, $previous);
    }
}
