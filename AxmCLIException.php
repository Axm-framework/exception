<?php

namespace Axm\Exception;

use Axm;
use Axm\Console\CLI;

class AxmCLIException
{

    public static function handleCLIException(\Throwable $e): self
    {
        $title = get_class($e);
        $message = $e->getMessage();

        // Print the main exception information
        CLI::newLine();
        CLI::write('[' . $title . ': ' . $message . ']', 'light_red');
        CLI::newLine();
        CLI::write('at ' . CLI::color(($e->getFile()) . ':' . $e->getLine(), 'green'));
        CLI::newLine();

        // Print the backtrace if not in production environment
        if (!Axm::isProduction()) {
            $backtraces = $e->getTrace();

            if ($backtraces) {
                CLI::write('Backtrace:', 'blue');
            }

            foreach ($backtraces as $i => $error) {
                $padFile = '    '; // 4 spaces
                $padClass = '       '; // 7 spaces
                $c = str_pad($i + 1, 3, ' ', STR_PAD_LEFT);

                // Print the file path and line number
                if (isset($error['file'])) {
                    $filepath = cleanPath($error['file']) . ':' . $error['line'];

                    CLI::write($c . $padFile . CLI::color($filepath, 'yellow'));
                } else {
                    CLI::write($c . $padFile . CLI::color('[internal function]', 'yellow'));
                }

                // Print the function and its arguments
                $function = '';

                if (isset($error['class'])) {
                    $type = ($error['type'] === '->') ? '()' . $error['type'] : $error['type'];
                    $function .= $padClass . $error['class'] . $type . $error['function'];
                } elseif (!isset($error['class']) && isset($error['function'])) {
                    $function .= $padClass . $error['function'];
                }

                if (isset($error['args'])) {
                    $args = array_map(function ($arg) {
                        if (is_object($arg)) {
                            return 'Object(' . get_class($arg) . ')';
                        }

                        if (is_array($arg)) {
                            return count($arg) ? static::formatArray($arg) : '[]';
                        }

                        if (is_string($arg)) {
                            return "'" . $arg . "'";
                        }

                        if (is_bool($arg)) {
                            return $arg ? 'true' : 'false';
                        }

                        return $arg;
                    }, $error['args']);

                    $function .= '(' . implode(', ', $args) . ')';
                } else {
                    $function .= '()';
                }

                CLI::write($function);

                // Print the horizontal separator
                CLI::write(str_repeat('-', strlen($function)), 'green');

                CLI::newLine();
            }
        }

        exit(1);
    }


    private static function formatArray($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_object($key)) {
                $result[] = '(Object(Closure))';
            } else if (is_array($key)) {
                if (is_object($key[0])) {
                    $result[] = '(Object(Closure))';
                } else {
                    $result[] = self::formatArray($key) . '=>' . (is_object($value) ? get_class($value) : $value);
                }
            } else {
                $result[] = $key . '=>' . (is_object($value) ? get_class($value) : $value);
            }
        }

        return "[" . implode(", ", $result) . "]";
    }
}
