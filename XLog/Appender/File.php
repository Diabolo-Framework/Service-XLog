<?php
namespace X\Service\XLog\Appender;
class File extends AppenderBase {
    /** 文件路径 */
    protected $path = null;
    /** 启用每日文件 */
    protected $enableDailyFile = false;
    
    /** 文件handle */
    private $fileHandler = null;
    
    /**
     * {@inheritDoc}
     * @see \X\Service\XLog\Appender\AppenderBase::init()
     */
    protected function init() {
        if ( $this->enableDailyFile ) {
            $this->path .= '-'.date('Y-m-d');
        }
        $this->fileHandler = fopen($this->path, 'a');
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\XLog\Appender\AppenderInterface::flush()
     */
    public function flush() {
        if ( empty($this->caches) ) {
            return;
        }
        
        $logs = array();
        foreach ( $this->caches as $log ) {
            $logs[] = $this->formatLog($log);
        }
        $logs = implode("\n", $logs)."\n";
        fwrite($this->fileHandler, $logs);
        
        $this->clearCache();
    }
    
    /**
     * 格式化日志信息
     * @param unknown $log
     * @return string
     */
    private function formatLog( $log ){
        return "{$log['time']} - {$log['type']} - {$log['content']}";
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\XLog\Appender\AppenderBase::close()
     */
    public function close() {
        $this->flush();
        fclose($this->fileHandler);
    }
}