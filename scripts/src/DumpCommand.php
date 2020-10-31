<?php

namespace LegoMedia\LegoLand;

use PhpBinaryReader\BinaryReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dump-res')
            ->addArgument('res', InputArgument::REQUIRED, '.res file')
            ->addArgument('dest', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = fopen($input->getArgument('res'), 'rb');

        $basedir = $input->getArgument('dest');

        $br = new BinaryReader($file);
        $dataStart = $br->readUInt32();

        $br->setPosition($dataStart);
        $entries = $this->readEntry($br, $dataStart);

        foreach ($entries as $e) {
            $this->saveEntry($e, $basedir, $file);
        }

        return 0;
    }

    protected function readEntry(BinaryReader $br, $startOffset)
    {
        $e = ResEntry::read($br);

        if ($e->a != 0xFFFFFFFF) {
            $br->setPosition($startOffset + $e->a);
            $e->children = $this->readEntry($br, $startOffset);
        }

        $siblings = [];
        if ($e->b != 0xFFFFFFFF) {
            $br->setPosition($startOffset + $e->b);
            $siblings = $this->readEntry($br, $startOffset);
        }

        return array_merge([$e], $siblings);
    }

    private function saveEntry(ResEntry $entry, $basedir, $fh)
    {
        $path = $basedir . '/' . $entry->name;
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        if (!$entry->isFolder) {
            $dest = fopen($path, 'w+');
            fseek($fh, $entry->e);
            stream_copy_to_stream($fh, $dest, $entry->d);
        }


        foreach ($entry->children as $child) {
            $this->saveEntry($child, $path, $fh);
        }
    }
}
