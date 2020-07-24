<?php

namespace HimmelKreis4865\BetterAC\utils;

class LanguageManager {
    /** @var null|Language $language */
    private $language = null;

    /**
     * LanguageManager constructor.
     *
     * @param Language $language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * @return Language
     */
    public function getLanguage() :Language {
        return $this->language;
    }
}