<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\I18N;

final class I18N implements \Htw\I18N\I18NInterface
{
    private array $data;

    public function __construct(
        string $translations_path,
        string $locale
    ) {
        $filename = $translations_path . $locale . '.php';

        if (file_exists($filename)) {
            $this->data = require $filename;
        }
    }

    public function translate(string $id, array $params = []): string
    {
        if (isset($this->data[$id])) {
            return $this->data[$id];
        }
        return $id;
    }

    public function findId(string $translation): string
    {
        $id = array_search($translation, $this->data);

        if (false === $id) {
            return $translation;
        }

        return $this->data[$id];
    }
}
