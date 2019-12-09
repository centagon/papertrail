<?php

namespace StephaneCoinon\Papertrail;

use Monolog\Handler\SyslogUdpHandler;
use DateTimeInterface;

class CustomSyslogUdpHandler extends SyslogUdpHandler {

    protected $hostname;

    private $customDateFormats = array(
        self::RFC3164 => 'M d H:i:s',
        self::RFC5424 => \DateTime::RFC3339,
    );

    /**
     * Make common syslog header (see rfc5424)
     */
    protected function makeCommonSyslogHeader($severity, DateTimeInterface $datetime): string
    {
        $priority = $severity + $this->facility;

        if (!$pid = getmypid()) {
            $pid = '-';
        }

        if (!$hostname = ($this->hostname ? : gethostname()) ) {
            $hostname = '-';
        }

        if ($this->rfc === self::RFC3164) {
            $datetime->setTimezone(new \DateTimeZone('UTC'));
        }

        $date = $datetime->format($this->customDateFormats[$this->rfc]);

        if ($this->rfc === self::RFC3164) {
            return "<$priority>" .
                $date . " " .
                $hostname . " " .
                $this->ident . "[" . $pid . "]: ";
        } else {
            return "<$priority>1 " .
                $date . " " .
                $hostname . " " .
                $this->ident . " " .
                $pid . " - - ";
        }
    }

    public function setHostName($hostname) {
        $this->hostname = $hostname;
        return $this;
    }

}
