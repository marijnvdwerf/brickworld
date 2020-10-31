<?php

namespace LegoMedia\LegoLand;

use PhpBinaryReader\BinaryReader;

class ResEntry
{
    public $a;
    public $b;
    public $isFolder;
    public $d;
    public $e;
    public $name = '';

    /** @var ResEntry[] */
    public $children = [];

    public static function read(BinaryReader $br)
    {
        $e = new self();
        $e->a = $br->readUInt32(); // 0x00
        $e->b = $br->readUInt32(); // 0x04
        $e->isFolder = (bool)$br->readUInt32(); // 0x08
        $e->d = $br->readUInt32(); // 0x0C
        $e->e = $br->readUInt32(); // 0x10

        while (true) {
            $chr = $br->readString(1);
            if ($chr === "\0") break;
            $e->name .= $chr;
        }

        return $e;
    }
}
