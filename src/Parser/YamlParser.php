<?php

namespace Swindon\YamlToFilament\Parser;

use Symfony\Component\Yaml\Yaml;

class YamlParser
{
    public function parseFile($path)
    {
        // Check if $path is a file or a directory.
        if (is_dir($path)) {
            $data = [];
            foreach (glob($path . '/*.y{a,}ml', GLOB_BRACE) ?: [] as $file) {
                $data = array_merge($data, Yaml::parseFile($file));
            }
            return $data;
        } elseif (is_file($path)) {
            return Yaml::parseFile($path);
        }
        
        throw new \InvalidArgumentException("The provided path '{$path}' is neither a valid file nor a directory.");
    }
}
