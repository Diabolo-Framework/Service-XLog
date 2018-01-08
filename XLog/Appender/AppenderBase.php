<?php 
namespace X\Service\XLog\Appender;
abstract class AppenderBase implements AppenderInterface {
    /** 日志缓存列表 */
    protected $caches = array();
    /** 清空缓存 */
    protected function clearCache() {
        $this->caches = array();
    }
    /** 是否缓存日志 */
    protected $cache = false;
    
    /**
     * {@inheritDoc}
     * @see \X\Service\XLog\Appender\AppenderInterface::__construct()
     */
    public function __construct( array $option) {
        foreach ( $option as $key => $value ) {
            if ( property_exists($this, $key) ) {
                $this->$key = $value;
            }
        }
        $this->init();
    }
    
    /** 初始化 */
    protected function init() {}
    
    /**
     * {@inheritDoc}
     * @see \X\Service\XLog\Appender\AppenderInterface::append()
     */
    public function append( $type, $content ) {
        $this->caches[] = array(
            'time' => microtime(true),
            'type' => $type,
            'content' => $content,
        );
        if ( !$this->cache ) {
            $this->flush();
        }
    }
}