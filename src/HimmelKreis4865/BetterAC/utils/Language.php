<?php

namespace HimmelKreis4865\BetterAC\utils;

class Language
{
    /** @var array $messages */
    private $messages;

    /** @var string $name */
    private $name;

    public function __construct(string $name, array $messages)
    {
        $this->messages = $messages;
        $this->name = $name;
    }

    /**
     * Translate a config string value to message
     *
     * @api
     *
     * @param string $message
     * @param array $searches
     * @param array $replaces
     *
     * @return string
     */
    public function translateString(string $message, array $searches = [], array $replaces = []) :string{
        if (!isset($this->messages[$message])) return "";
        return str_replace($searches, $replaces, $this->messages[$message]);
    }



    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getMessageArray(): array
    {
        return $this->messages;
    }
}