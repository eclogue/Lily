<?php
/**
 * @license MIT
 * @copyright Copyright (c) 2018
 * @author: bugbear
 * @date: 2018/3/30
 * @time: 下午3:07
 */

namespace Lily;

use Symfony\Component\Yaml\Yaml;

class Parser
{
    public $root = '';

    public $file = '';

    public $pointer;

    public $current  = '';

    public $step = 0;
    
    public function __construct(string $root, string $file)
    {
        $this->root = $root;
        $this->file = $file;
        $this->pointer = new \SplStack();
    }

    public function run()
    {
        $this->current = $this->root;
        $file = $this->root . '/' . rtrim($this->file, './');
        $documents = $this->parse($file);
        $documents = $this->sniffer($documents);

        return $documents;
    }

    /**
     * @param $file
     * @return mixed
     */
    public function parse($file)
    {
        try {
            if ($this->pointer->count()) {
                $this->current = $this->pointer->pop();
            }

            $document = Yaml::parseFile($file);
            if (!is_array($document)) {
                return ltrim($file, $this->root);
            }

            return $document;
        } catch (\Throwable $exception) {
            return ltrim($file, $this->root);
        }
    }

    public function sniffer(array $child)
    {
        $refer = false;
        foreach ($child as $key => &$node) {
            if ($key === '$ref' && !is_array($node)) {
                $info = pathinfo($node);
                $ext = $info['extension'];
                if ($ext === 'yaml' || $ext === 'yml') {
                    echo "%%%%%" . $node . PHP_EOL;
                    $dirname = ltrim($info['dirname'], './');
                    $pointer = $this->current;
                    $this->pointer->push($pointer);
                    $refer = true;
                    if ($dirname) {
                        $pointer .= '/' . $dirname;
                    }

                    $this->pointer->push($pointer);
                    $file = $pointer . '/' . $info['basename'];
                    $node = $this->parse($file);
                    if ($this->pointer->count()) {
                        $this->current = $this->pointer->pop();
                    }
                }
            }

            if (is_array($node)) {
//                $this->pointer->push($this->current);
                $node = $this->sniffer($node);
                if ($refer && $this->pointer->count()) {
                    $this->current = $this->pointer->pop();
                    $refer = false;
                }

            }
        }

        unset($node);

        return $child;
    }
}
