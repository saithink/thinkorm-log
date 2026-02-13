<?php
namespace Guozhijian611\ThinkOrmLog;

/**
 * 插件安装
 */
class Install
{
    const WEBMAN_PLUGIN = true;

    /**
     * @var array
     */
    protected static $pathRelation = [
        'config/plugin/guozhijian611/thinkorm-log' => 'config/plugin/guozhijian611/thinkorm-log',
    ];

    /**
     * Install
     * @return void
     */
    public static function install()
    {
        static::installByRelation();
        
        // 创建日志目录
        $logPath = runtime_path() . '/logs';
        if (!is_dir($logPath)) {
            mkdir($logPath, 0755, true);
        }

        echo "ThinkOrm日志插件安装完成！\n";
        echo "配置文件位置：config/plugin/guozhijian611/thinkorm-log/app.php\n";
        echo "日志文件位置：{$logPath}/sql.log, {$logPath}/api.log\n";
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
        self::uninstallByRelation();
        echo "ThinkOrm日志插件卸载完成！\n";
    }

    /**
     * installByRelation
     * @return void
     */
    public static function installByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path().'/'.substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }
            copy_dir(__DIR__ . "/$source", base_path()."/$dest");
            echo "Create $dest\n";
        }
    }

    /**
     * uninstallByRelation
     * @return void
     */
    public static function uninstallByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            $path = base_path()."/$dest";
            if (!is_dir($path) && !is_file($path)) {
                continue;
            }
            echo "Remove $dest\n";
            if (is_file($path) || is_link($path)) {
                unlink($path);
                continue;
            }
            remove_dir($path);
        }
    }
}
