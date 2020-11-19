<?php

declare(strict_types=1);

namespace Overfair\TelegramParser;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class Parser
{
    public const CONNECT_TIMEOUT_DEFAULT = 7;
    public const REQUEST_TIMEOUT_DEFAULT = 10;

    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @var ResponseInterface|null
     */
    public ?responseInterface $last_response = null;

    /**
     * @var int
     */
    public int $connect_timeout = self::CONNECT_TIMEOUT_DEFAULT;

    /**
     * @var int
     */
    public int $request_timeout = self::REQUEST_TIMEOUT_DEFAULT;

    /**
     * Parser constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    public function request(string $username, array $get = null): string
    {
        $uri = 'https://t.me/s/'.$username;

        if($get !== null){
            $uri .= '?' . http_build_query($get);
        }

        $this->last_response = $this->client->post($uri, [
            RequestOptions::CONNECT_TIMEOUT => $this->connect_timeout,
            RequestOptions::TIMEOUT => $this->request_timeout,
            RequestOptions::HEADERS => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
                'Accept-Encoding' => 'gzip, deflate, br',
                'DNT' => '1',
                'Connection' => 'keep-alive',
                'Referer' => "https://t.me/s/$username",
                'Upgrade-Insecure-Requests' => '1',
                'X-Requested-With' => 'XMLHttpRequest',
                'Origin' => 'https://t.me',
            ]
        ]);

        $response_contents = $this->last_response->getBody()->getContents();
        return json_decode($response_contents, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $username
     * @param int|null $before
     * @param string|null $query
     * @return Message[]
     */
    public function getMessages(string $username, int $before = null, string $query = null): array
    {
        $response = $this->request($username, [
            'before' => $before,
            'q' => $query,
        ]);

        if(!preg_match_all('|<div class="tgme_widget_message_wrap ' .
            'js-widget_message_wrap">(.*?)(</div>\s*){4}|sui', $response, $messages_matches)){
            throw new \Error('Messages not found');
        }

        $result = [];
        $i = 0;
        do{
        foreach ($messages_matches[1] as $message_html) {
            $result[] = (array)Message::createByHtml($message_html);
            $i++;
        }
        } while ($i <= 8);
        exit(print_r($result));
        return $result;
    }


}