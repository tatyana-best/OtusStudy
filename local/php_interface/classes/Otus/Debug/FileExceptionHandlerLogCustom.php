<?php

namespace Otus\Debug;

use Bitrix\Main\Diag\FileExceptionHandlerLog;
use Bitrix\Main\Diag\ExceptionHandlerFormatter;

/**
 * overwrite system debug class FileExceptionHandlerLog
 */
class FileExceptionHandlerLogCustom extends FileExceptionHandlerLog
{
    const DEFAULT_LOG_FILE = "local/logs/log.txt";

    /**
     * update system debug function writeToLog
     * write Otus word before text
     */
    public function writeToLog($text)
    {
        $this->logger->debug('OTUS: ' . $text);
    }

    /**
     * update system debug function write
     * write Otus word before message
     */
    public function write($exception, $logType)
    {
        $text = ExceptionHandlerFormatter::format($exception, false, $this->level);

        $context = [
            'type' => static::logTypeToString($logType),
        ];

        $logLevel = static::logTypeToLevel($logType);

        $message = "OTUS: {date} - Host: {host} - {type} - {$text}\n";

        $this->logger->log($logLevel, $message, $context);
    }
}
