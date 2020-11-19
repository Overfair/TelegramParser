<?php
declare(strict_types=1);

namespace Overfair\TelegramParser;


class User
{
    /**
     * @var string
     */
    public string $link;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $picture;

    /**
     * User constructor.
     * @param string $link
     */
    public function __construct(string $link)
    {
        $this->link = $link;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function makeByTelegramChannel(array $data): self
    {
        $link = $data['link'];
        $result = new self($link);
        $result->name = $data['name'];
        $result->picture = $data['picture'];

        return $result;
    }
}