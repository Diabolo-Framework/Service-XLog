<?php
namespace X\Service\XLog\Appender;
class Database extends AppenderBase {
    /** @var \PDO */
    private $connection = null;
    protected $dsn = null;
    protected $user = null;
    protected $password = null;
    protected $table = null;
    protected $attrs = array(
        'time' => '$time',
        'type' => '$type',
        'content' => '$cotent',
    );
    protected $enableGroupContent = false;
    
    /**
     * {@inheritDoc}
     * @see \X\Service\XLog\Appender\AppenderBase::init()
     */
    protected function init() {
        $this->connection = new \PDO($this->dsn, $this->user, $this->password);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\XLog\Appender\AppenderInterface::flush()
     */
    public function flush() {
        if ( empty($this->caches) ) {
            return;
        }
        
        $sql = array('INSERT INTO',$this->table,'(');
        $sql[] = implode(',', array_keys($this->attrs));
        $sql[] = ') VALUES';
        
        if ( !$this->enableGroupContent ) {
            $sql[] = $this->getSingleValues();
        } else {
            $sql[] = $this->getMixedLogValues();
        }
        
        $sql = implode(' ', $sql);
        $insertRowCount = $this->connection->exec($sql);
        if ( false === $insertRowCount ) {
            $error = $this->connection->errorInfo();
            throw new \Exception("XLog save log error : {$error[2]}");
        }
        $this->clearCache();
    }
    
    /**
     * 获取单独的value列表
     */
    private function getSingleValues() {
        $logs = array();
        foreach ( $this->caches as $item ) {
            $log = array();
            foreach ( $this->attrs as $key => $value ) {
                if ( '$' === $value[0] ) {
                    $log[] = $this->connection->quote($item[substr($value, 1)]);
                } else {
                    $log[] = $this->connection->quote($value);
                }
            }
            $logs[] = '('.implode(',', $log).')';
        }
        return implode(',', $logs);
    }
    
    /**
     * 获取组合到一起的value值
     */
    private function getMixedLogValues() {
        $log = array();
        foreach ( $this->attrs as $key => $value ) {
            if ( '$' === $value[0] ) {
                switch ( $value ) {
                case '$type' : 
                    $log[] = $this->connection->quote('mixed');
                    break;
                case '$content' :
                    $content = array();
                    foreach ( $this->caches as $item ) {
                        $content[] = "{$item['type']} - {$item['content']}";
                    }
                    $content = implode("\n", $content);
                    $log[] = $this->connection->quote($content);
                    break;
                default :
                    $log[] = $this->connection->quote($this->caches[0][substr($value, 1)]);
                    break;
                }
            } else {
                $log[] = $this->connection->quote($value);
            }
        }
        return '('.implode(',', $log).')';
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\XLog\Appender\AppenderInterface::close()
     */
    public function close() {
        $this->flush();
        $this->connection = null;
    }
}