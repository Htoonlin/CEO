<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/16/2015
 * Time: 6:08 PM
 */

namespace Application\Service;

use Zend\Db\Adapter\Profiler\ProfilerInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriter;
use Zend\Log\Formatter\Simple as LogFormatter;

/**
 * Class SundewLogger
 * @package Application\Service
 */
class SundewLogger implements ProfilerInterface{
    protected $logger;
    protected $current_user;

    /**
     * @param string $filename
     * @param SundewAuthStorage $user
     */
    function __construct($filename, $user = null)
    {
        if($user){
            $this->current_user = get_object_vars($user);
        }else{
            $this->current_user = array();
        }

        $logger = new Logger();
        $writer = new LogWriter('./data/logs/' . $filename);
        $format = '%priorityName%: %timestamp%';
        if(!empty($this->current_user)){
            $format .= PHP_EOL . 'Extra: %extra%';
        }
        $format .= PHP_EOL . "=============================================================" . PHP_EOL;
        $format .= '%message%';
        $format .= PHP_EOL . "=============================================================" . PHP_EOL;
        $formatter = new LogFormatter($format);
        $writer->setFormatter($formatter);
        $logger->addWriter($writer);
        $this->logger = $logger;
    }

    /**
     * @param \Exception $e
     */
    public function logException(\Exception $e){
        $i = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e = $e->getPrevious());

        $log = implode(PHP_EOL, $messages);

        $this->logger->err($log, $this->current_user);
    }

    private function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    /**
     * @param string|\Zend\Db\Adapter\StatementContainerInterface $target
     * @return bool
     */
    public function profilerStart($target)
    {
        if(is_object($target))
        {
            $query = $target->getSql();

            if(!$this->startsWith($query, 'SELECT')){
                $log = 'Statement : ' . $query;
                if($target->getParameterContainer()->count() > 0){
                    $log .= PHP_EOL . 'Parameter : ' . json_encode($target->getParameterContainer()->getNamedArray());
                }
                $this->logger->debug($log, $this->current_user);
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function profilerFinish()
    {
        return true;
    }
}