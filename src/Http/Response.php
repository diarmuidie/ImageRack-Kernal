<?php

namespace Diarmuidie\ImageRack\Http;

use League\Flysystem\File;

class Response
{
    private $body;

    public function setHeader($header)
    {
        header($header);
    }

    public function setStatusCode($code)
    {
        http_response_code($code);
    }

    public function setNotFoundHeader()
    {
        $this->setStatusCode(404);
    }

    public function sendFile(File $file)
    {
        $this->setContentType($file->getMimetype());
        $this->setContentLength($file->getSize());
        $this->setLastModified($file->getTimestamp());

        $this->stream($file->readStream());
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function setContentType($contentType)
    {
        $this->setHeader('Content-Type: ' . $contentType);
    }

    public function setContentLength($contentLength)
    {
        $this->setHeader('Content-Length: ' . $contentLength);
    }

    public function setLastModified($lastModified)
    {
        $dt = new \DateTime();
        $dt->setTimestamp($lastModified);
        $this->setHeader('Last-Modified: ' . $dt->format('D, d M Y H:i:s \G\M\T'));
    }

    private function obSize()
    {
        $bufferLength = ini_get('output_buffering');
        if ($bufferLength > 0) {
            return $bufferLength;
        };
        return 4096;
    }

    public function stream($stream)
    {
        $obLength = $this->obSize();
        ob_start();
        while (!feof($stream)) {
            echo stream_get_contents($stream, $obLength);
            ob_flush();
            flush();
        }
    }

    public function send()
    {
        echo $this->body;
    }
}
