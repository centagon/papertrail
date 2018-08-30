<?php

namespace StephaneCoinon\Papertrail;

use Monolog\Handler\SyslogUdpHandler;

class CustomSyslogUdpHandler extends SyslogUdpHandler {

    protected $hostname;
    
    /**
     * Make common syslog header (see rfc5424)
     */
    protected function makeCommonSyslogHeader($severity)
    {
        $priority = $severity + $this->facility;

        if (!$pid = getmypid()) {
            $pid = '-';
        }

        if (!$hostname = ($this->hostname ? : gethostname()) ) {
            $hostname = '-';
        }

        return "<$priority>1 " .
            $this->getDateTime() . " " .
            $hostname . " " .
            $this->ident . " " .
            $pid . " - - ";
    }

    public function setHostName($hostname) {
        $this->hostname = $hostname;
        return $this;
    }
    
}
