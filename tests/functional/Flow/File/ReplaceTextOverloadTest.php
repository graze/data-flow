<?php

namespace Graze\DataFlow\Test\Functional\Flow\File;

use Graze\DataFlow\Flow\File\ReplaceText;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ReplaceTextOverloadTest extends FileTestCase
{
    /**
     * @var ReplaceText
     */
    protected $replacer;

    public function setUp()
    {
        $this->replacer = new ReplaceText();
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindEncoding()
    {
        $process = m::mock('overload:Symfony\Component\Process\Process');
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('isOutputDisabled')->andReturn(true);

        $file = new LocalFile(static::$dir . 'failed_replace_text.test');
        file_put_contents($file->getFilePath(), 'some text that text should be replaced');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->replacer->replaceText($file, 'text', 'pants');
    }
}
