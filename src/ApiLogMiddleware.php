<?php

namespace Guozhijian611\ThinkOrmLog;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use support\Log;

/**
 * API日志中间件
 */
class ApiLogMiddleware implements MiddlewareInterface
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param callable $handler
     * @return Response
     */
    public function process(Request $request, callable $handler): Response
    {
        // 获取配置
        $config = config('plugin.guozhijian611.thinkorm-log.app.api_log', [
            'enable' => true,
            'console' => true,
            'file' => false,
            'ignore_paths' => ['logs', 'logview', '/logs', '/logview/']
        ]);

        // 如果未启用API日志，直接处理请求
        if (!$config['enable']) {
            return $handler($request);
        }

        // 获取请求路径和URL
        $path = $request->path();
        $url = $request->url();

        // 检查是否需要忽略此请求
        foreach ($config['ignore_paths'] as $ignorePath) {
            if ($path === $ignorePath || 
                strpos($path, $ignorePath) === 0 || 
                strpos($url, $ignorePath) !== false) {
                // 直接处理请求，不记录日志
                return $handler($request);
            }
        }

        // 获取请求信息
        $method = $request->method();
        $params = $request->all();
        $headers = $request->header();

        // 记录请求开始时间
        $startTime = microtime(true);

        // 处理请求
        $response = $handler($request);

        // 计算请求耗时
        $endTime = microtime(true);
        $runtime = round(($endTime - $startTime) * 1000, 2);

        // 获取状态码
        $statusCode = $response->getStatusCode();

        // 获取返回数据
        $date = date('Y-m-d H:i:s');

        // 美化输出
        $output = "\n";
        $output .= "┌────────────────────────".$date."───────────────────────────\n";
        $output .= "│ 状态码: " . $this->getStatusEmoji($statusCode) . " {$statusCode}\n";
        $output .= "│ 请求方式: {$method}\n";
        $output .= "│ 接口: {$url}\n";
        $output .= "│ 耗时: {$runtime}ms\n";
        
        // 如果有请求头则输出请求头
        if (!empty($headers)) {
            $output.= "│ 请求头: ". json_encode($headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT). "\n";
        }
        
        // 如果有参数则输出参数
        if (!empty($params)) {
            $output .= "│ 参数: " . json_encode($params, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        }
        
        // 响应数据
        $responseBody = $response->rawBody();
        if (!empty($responseBody)) { 
            // 尝试解析JSON，如果是JSON则美化输出，否则直接输出
            $decodedBody = json_decode($responseBody, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $output .= "│ 响应数据: " . json_encode($decodedBody, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
            } else {
                $output .= "│ 响应数据: " . $responseBody . "\n";
            }
        }
        
        $output .= "└────────────────────────────────────────────────────\n";

        // 输出到控制台
        if ($config['console']) {
            echo $output;
        }

        // 记录到日志文件
        if ($config['file']) {
            Log::channel('plugin.guozhijian611.thinkorm-log.api')->info($output);
        }

        return $response;
    }

    /**
     * 根据状态码返回对应的表情
     */
    private function getStatusEmoji(int $code): string
    {
        return match (true) {
            $code >= 500 => '❌',  // 服务器错误
            $code >= 400 => '⚠️',  // 客户端错误
            $code >= 300 => '↪️',  // 重定向
            $code >= 200 => '✅',  // 成功
            default => '❓'        // 其他
        };
    }
} 