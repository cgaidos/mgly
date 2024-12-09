<?php

namespace Moowgly\Lib\Utils;

class Chrono
{
    private $start;

    private $end;

    public function __construct()
    {
        $this->start = microtime(true);
    }

    /**
     * Return elapsed time between Chrono class instantiation
     *  and this function call.
     *
     * @return elapsed time (in sec.)
     */
    public function stop()
    {
        $this->end = microtime(true);

        return $this->end - $this->start;
    }
}
