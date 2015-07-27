<?php

namespace Graze\DataFlow\Test\File;

use Graze\DataFlow\Test\TestCase;

abstract class FileTestCase extends TestCase
{
    const TEST_DATA_PATH = '../../data/';

    /**
     * @var string
     */
    protected static $dir;

    public static function setUpBeforeClass()
    {
        static::$dir = static::getTestDir();
    }

    public static function tearDownAfterClass()
    {
        if (is_dir(static::$dir)) {
            static::rmDirRecursive(static::$dir);
        }
    }

    /**
     * Get the directory used for testing file io
     *
     * @return string
     */
    private static function getTestDir()
    {
        date_default_timezone_set('UTC');
        $dir = __DIR__ . '/' . static::TEST_DATA_PATH . strftime('%Y%m%d-%H%M/');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    /**
     * Delete the folder and all files/folders within it
     *
     * @param $path
     * @return bool
     */
    private static function rmDirRecursive($path)
    {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$path/$file")) ? static::rmDirRecursive("$path/$file") : unlink("$path/$file");
        }
        return rmdir($path);
    }
}
