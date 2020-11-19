<?php

declare(strict_types=1);

namespace Overfair\TelegramParser;


class File
{
    public const TYPE = [
    ];
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $link;

    /**
     * File constructor.
     * @param string $link
     */
    public function __construct(string $link)
    {
        $this->link = $link;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function makeByTelegramFile(array $data):self
    {
        $link = $data['link'];
        $result = new self($link);
        $result->name = $data['name'];

        return $result;
    }
}