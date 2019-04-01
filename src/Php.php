<?php

namespace StephaneCoinon\Papertrail;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

class Php {

    public static $defaultLoggerName = 'PHP';

    /**
     * Papertrail log handler.
     *
     * @var \Monolog\Handler\HandlerInterface
     */
    protected $handler;

    /**
     * Make a new PHP driver to send logs to Papertrail.
     *
     * @param  string $host   Papertrail log server, ie log.papertrailapp.com
     * @param  int $port      Papertrail port number for log server
     * @param  string $prefix Prefix to use for each log message
     * @param  string $hostname The hostname to use when logging messages
     */
    protected function __construct($host, $port, $prefix, $hostname = null, $log_level = null)
    {
        $log_level or $log_level = Logger::DEBUG;
        $this->handler = $this->getHandler($host, $port, $prefix, $hostname, $log_level);
    }

    /**
     * Boot connector with given host, port and log message prefix.
     * 
     * If host or port are omitted, we'll try to get them from the environment
     * variables PAPERTRAIL_HOST and PAPERTRAIL_PORT.
     * 
     * @param  string $host   Papertrail log server, ie log.papertrailapp.com
     * @param  int $port      Papertrail port number for log server
     * @param  string $prefix Prefix to use for each log message
     * @return \Psr\Log\LoggerInterface
     */
    public static function boot($host = null, $port = null, $prefix = '', $hostname = '', $log_level = null)
    {
        $host or $host = getenv('PAPERTRAIL_HOST');
        $port or $port = getenv('PAPERTRAIL_PORT');
        $hostname or $hostname = getenv('PAPERTRAIL_HOSTNAME');
        $log_level or $log_level = getenv('APP_LOG_LEVEL');
        $prefix and $prefix = "[$prefix] ";

        return (new static($host, $port, $prefix, $hostname, $log_level))
                        ->detectFrameworkOrFail()
                        ->registerPapertrailHandler();
    }

    /**
     * Boot connector using credentials set in environment variables and the
     * given log message prefix.
     * 
     * @param string $prefix Prefix to use for each log message
     */
    public static function bootWithPrefix($prefix)
    {
        return static::boot(null, null, $prefix);
    }

    /**
     * Get Papertrail SysLog handler.
     *
     * @param string $host
     * @param int $port
     * @param string $prefix
     * @return \Monolog\Handler\HandlerInterface
     */
    public function getHandler($host, $port, $prefix, $hostname, $log_level = Logger::DEBUG)
    {
        $syslog = new CustomSyslogUdpHandler($host, $port, LOG_USER, $log_level);
        $syslog->setHostName($hostname);
        $formatter = new LineFormatter("$prefix%channel%.%level_name%: %message% %extra%");
        $syslog->setFormatter($formatter);

        return $syslog;
    }

    /**
     * Get the logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return new \Monolog\Logger(static::$defaultLoggerName);
    }

    /**
     * Throw an exception if the framework for this driver is not detected
     *
     * @return $this
     * @throws FrameworkNotDetectedException
     */
    protected function detectFrameworkOrFail()
    {
        // no framework to detect in a plain PHP context
        return $this;
    }

    /**
     * Register papertrail log handler with the current logger.
     *
     * @return \Psr\Log\LoggerInterface
     */
    protected function registerPapertrailHandler()
    {
        return $this->getLogger()->pushHandler($this->handler);
    }

}
