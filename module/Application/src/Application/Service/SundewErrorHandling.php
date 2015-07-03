<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/3/2015
 * Time: 6:37 PM
 */

namespace Application\Service;


class SundewErrorHandling {
    protected $logger;

    function __construct($logger)
    {
        $this->logger = $logger;
    }

    function logException(\Exception $e){
        $trace = $e->getTraceAsString();
        $i = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e = $e->getPrevious());

        $log  = "\n================ Exception ================\n";
        $log .= implode("\n", $messages);
        $log .= "\n================ Trace ================\n";
        $log .= $trace;
        $log .= "\n================ END ================\n";

        $this->logger->err($log);
    }
}