<?php
namespace X\Service\XLog;
use X\Service\XLog\Appender\AppenderInterface;

/**
 * @author Michael Luthor
 * @method static void trace($message)
 * @method static void debug($message)
 * @method static void info($message)
 * @method static void warn($message)
 * @method static void error($message)
 * @method static void fatal($message)
 */
class Logger {
    /**
     * 日志等级列表
     * @var array
     */
    private $logLevels = array(
        'trace' => 1,
        'debug' => 2,
        'info'  => 3,
        'warn'  => 4,
        'error' => 5,
        'fatal' => 6,
    );
    
    /** 
     * 默认日志级别 
     * */
    private $logLevel = 1;
    
    /**
     * 设置日志级别
     * @param unknown $level
     */
    public function setLogLevel( $level ) {
        $this->logLevel = $this->logLevels[$level];
    }
    
    /**
     * @var AppenderInterface
     */
    private $appender = null;
    
    /**
     * @param AppenderInterface $appender
     */
    public function __construct( AppenderInterface $appender ) {
        $this->appender = $appender;
    }
    
    /**
     * @var Logger
     */
    private static $logger = null;
    
    /**
     * @param unknown $name
     * @param unknown $content
     */
    public static function __callstatic( $name, $params ) {
        if ( null === self::$logger ) {
            self::$logger = Service::getService()->getLogger();
        }
        $logger = self::$logger;
        if ( !isset($logger->logLevels[$name]) || $logger->logLevels[$name] < $logger->logLevel ) {
            return;
        }
        $logger->appender->append($name, call_user_func_array('sprintf', $params));
    }
    
    /**
     * 关闭日志
     */
    public function close () {
        $this->appender->close();
    }
}