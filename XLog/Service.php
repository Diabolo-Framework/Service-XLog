<?php
namespace X\Service\XLog;
use X\Service\XLog\Appender\AppenderInterface;

/**
 * @author michael
 */
class Service extends \X\Core\Service\XService {
    /**
     * @var string
     */
    protected static $serviceName = 'XLog';
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::start()
     */
    public function start() {
        parent::start();
        
        $config = $this->getConfiguration();
        
        $appender = null;
        
        $appendHandler = $config->get('handler','file');
        $appenderClass = sprintf('\\X\\Service\\XLog\\Appender\\%s', ucfirst($appendHandler));
        if ( !class_exists($appenderClass) || ( $appenderClass instanceof AppenderInterface ) ) {
            throw new \Exception("Log handler `{$appendHandler}` has not been supported.");
        }
        
        $appender = new $appenderClass($config->toArray());
        $logger = new Logger($appender);
        $logger->setLogLevel($config->get('level', 'trace'));
        $this->logger = $logger;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::stop()
     */
    public function stop() {
        $this->getLogger()->close();
        parent::stop();
    }
    
    /**
     * @var AppenderInterface
     */
    private $logger = null;
    
    /**
     * @var AppenderInterface 
     */
    public function getLogger() {
        return $this->logger;
    }
}