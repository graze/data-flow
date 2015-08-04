<?php

namespace Graze\DataFlow\Flow\File\Transfer;

use Graze\DataFlow\Flow\File\Exception\TransferFailedException;
use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\FileNode;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use League\Flysystem\MountManager;

class Transfer extends Flow implements ExtensionInterface, FileTransferInterface
{
    /**
     * @param ExtensibleInterface $extensible
     * @param string              $method
     * @return bool
     */
    public function canExtend(ExtensibleInterface $extensible, $method)
    {
        return (($extensible instanceof FileNode) &&
            ($method == 'copyTo' || $method == 'moveTo'));
    }

    /**
     * {@inheritdoc}
     */
    public function copyTo(FileNode $from, FileNode $to)
    {
        $mountManager = new MountManager([
            'from' => $from->getFilesystem(),
            'to'   => $to->getFilesystem()
        ]);

        if (!$mountManager->copy('from://' . $from->getPath(), 'to://' . $to->getPath())) {
            throw new TransferFailedException($from, $to);
        }

        return $to;
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo(FileNode $from, FileNode $to)
    {
        $mountManager = new MountManager([
            'from' => $from->getFilesystem(),
            'to'   => $to->getFilesystem()
        ]);

        if (!$mountManager->move('from://'.$from->getPath(), 'to://'.$to->getPath())) {
            throw new TransferFailedException($from, $to);
        }

        return $to;
    }
}
