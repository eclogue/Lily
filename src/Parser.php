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

    /**
     * @return array|mixed
     * @throws \Exception
     */
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
     * @throws \Exception
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
            $message = $exception->getMessage();
            throw new \Exception('File of ' . $file . ' parse error:' . $message);
        }
    }

    /**
     * @param array $child
     * @return array
     * @throws \Exception
     */
    public function sniffer(array $child)
    {
        $refer = false;
        foreach ($child as $key => &$node) {
            if ($key === '$ref' && !is_array($node)) {
                $info = pathinfo($node);
                $ext = $info['extension'];
                if ($ext === 'yaml' || $ext === 'yml') {
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
                }
            }

            if (is_array($node)) {
                $node = $this->sniffer($node);
            }
        }

        unset($node);

        if ($refer && $this->pointer->count()) {
            $this->current = $this->pointer->pop();
        }

        return $child;
    }
}