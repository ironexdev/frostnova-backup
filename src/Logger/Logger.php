<?php

namespace App\Logger;

use Exception;
use Monolog\Logger as MonologLogger;

class Logger extends MonologLogger
{
    public function info($message, array $context = [], Exception $e = null): void
    {
        if($e)
        {
            $context["message"] = $e->getMessage();
            $context["file"] = $e->getFile();
            $context["line"] = $e->getLine();
            $context["trace"] = $e->getTraceAsString(); // Trace is or is not included based on Monolog setting includeStacktraces in DI config
        }

        $this->addRecord(static::INFO, (string) $message, $context);
    }
}
