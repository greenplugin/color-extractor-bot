<?php
declare(strict_types=1);

namespace App\Service;


use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class TelegramDownloader
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $url
     * @param string $path
     * @return \SplFileInfo
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function download(string $url, string $path): \SplFileInfo
    {
        $request = new Request('GET', $url);
        $response = $this->client->sendRequest($request);
        $file = new \SplFileInfo($path);
        if (!$file->openFile('w')->isWritable()) {
            throw new AccessDeniedException(sprintf('%s file is not writable', $file->getPath()));
        }

        file_put_contents($file->getPath(), $response->getBody());
        return $file;
    }
}