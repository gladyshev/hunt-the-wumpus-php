<?php
/**
 * @project Hunt the Wumpus
 */

namespace Wumpus\UI;

interface I18NInterface
{
    public function translate(string $id, array $params = []): string;
    public function findId(string $translation): string;
}
