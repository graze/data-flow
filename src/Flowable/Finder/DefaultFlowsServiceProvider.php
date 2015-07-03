<?php

namespace Graze\DataFlow\Flowable\Finder;

use League\Container\ServiceProvider;

class DefaultFlowsServiceProvider extends ServiceProvider
{
    /**
     * List of commands to register
     *
     * If just a command is specified 'command' then it is a global method on all Flowable objects
     * To restrict a command to a class prefix the command with the class name such as: 'FlowableObject::command'
     *
     * @var array
     */
    protected $provides = [
        'Graze\DataFlow\Node\File\LocalFile::compress',
        'Graze\DataFlow\Node\File\LocalFile::decompress',
        'Graze\DataFlow\Node\File\LocalFile::gzip',
        'Graze\DataFlow\Node\File\LocalFile::gunzip',
        'Graze\DataFlow\Node\File\LocalFile::zip',
        'Graze\DataFlow\Node\File\LocalFile::unzip',
        'Graze\DataFlow\Node\File\LocalFile::changeEncoding',
    ];

    /**
     * Register all the flows that provide the commands above
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add('Graze\DataFlow\Node\File\LocalFile::compress', 'Graze\DataFlow\Flow\File\Compression\CompressorFactory');
        $this->getContainer()->add('Graze\DataFlow\Node\File\LocalFile::decompress', 'Graze\DataFlow\Flow\File\Compression\CompressorFactory');
        $this->getContainer()->add('Graze\DataFlow\Node\File\LocalFile::gzip', 'Graze\DataFlow\Flow\File\Compression\Gzip');
        $this->getContainer()->add('Graze\DataFlow\Node\File\LocalFile::gunzip', 'Graze\DataFlow\Flow\File\Compression\Gzip');
        $this->getContainer()->add('Graze\DataFlow\Node\File\LocalFile::zip', 'Graze\DataFlow\Flow\File\Compression\Zip');
        $this->getContainer()->add('Graze\DataFlow\Node\File\LocalFile::unzip', 'Graze\DataFlow\Flow\File\Compression\Zip');
        $this->getContainer()->add('Graze\DataFlow\Node\File\LocalFile::changeEncoding', 'Graze\DataFlow\Flow\File\ConvertEncoding');
    }
}
