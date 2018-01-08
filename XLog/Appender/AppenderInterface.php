<?php
namespace X\Service\XLog\Appender;
interface AppenderInterface {
    function append ($type, $content);
    function flush();
    function close();
}
