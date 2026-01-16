<?php
namespace Saithink\ThinkOrmLog;

use Webman\Bootstrap;
use think\facade\Db;
use support\Log;

/**
 * 日志记录类
 */
class BootstrapLog implements Bootstrap
{
    public static function start($worker)
    {
        $config = config('plugin.saithink.thinkorm-log.app', [
            'enable' => true,
            'console'   => false,
            'file'  => true,
        ]);
        if (!$config['enable']) {
            return;
        }
        // 进行监听处理
        Db::listen(function($sql, $runtime) use ($config) {
            if ($sql === 'select 1' || $sql === 'SELECT 1 AS ping' || $sql === 'SELECT 1 AS ping FROM DUAL') {
                // 心跳
                return;
            }
            $time = floatval($runtime) * 1000;
            $log = $sql." [{$time}ms]";
            // 打印到控制台
            if ($config['console']) {
                echo PHP_EOL ."\033[36m" . "ThinkORM: " . date("Y-m-d H:i:s") . "\033[0m";
                echo PHP_EOL . "[SQL]" . "\033[32m" .$sql . "\033[0m";
                if ($time < 50) {
                    echo PHP_EOL . "[Time]" . "\033[32m" . $time . "ms" . "\033[0m";
                } else if ($time < 200) {
                    echo PHP_EOL . "[Time]" ."\033[33m" . $time . "ms" . "\033[0m";
                } else  {
                    echo PHP_EOL . "[Time]" . "\033[31m" . $time . "ms" . "\033[0m";
                }
            }
            // 记录到日志文件
            if ($config['file']) {
                Log::channel('plugin.saithink.thinkorm-log.sql')->info($log);
            }
        });
    }
}