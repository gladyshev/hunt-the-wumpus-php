<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\I18N;

final class I18N implements \Htw\I18N\I18NInterface
{
    private array $data;
    private array $fallbackData;

    public function __construct(
        string $translationsPath,
        string $locale,
        string $fallbackLocale = 'en_US'
    ) {
        $localeFilename = $translationsPath . $locale . '.php';

        if (file_exists($localeFilename)) {
            $this->data = require $localeFilename;
        }

        $fallbackLocaleFilename = $translationsPath . $fallbackLocale . '.php';

        if (file_exists($fallbackLocaleFilename)) {
            $this->fallbackData = require $fallbackLocaleFilename;
        }
    }

    public function translate(string $id, array $params = []): string
    {
        if (isset($this->data[$id])) {
            return $this->data[$id];
        }

        if (isset($this->fallbackData[$id])) {
            return $this->fallbackData[$id];
        }

        return $id;
    }

    public function findId(string $translation): string
    {
        $id = array_search($translation, $this->data);

        if (false !== $id) {
            return $this->data[$id];
        }

        $id = array_search($translation, $this->fallbackData);

        if (false !== $id) {
            return $this->fallbackData[$id];
        }

        return $translation;
    }
}
