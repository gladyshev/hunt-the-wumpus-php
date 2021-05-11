<?php
/**
 * @project Hunt the Wumpus
 */

namespace Wumpus\UI;

use Wumpus\GameRules\TextDeliveryInterface;

final class UI implements \Wumpus\GameRules\UIInterface
{
    private TextDeliveryInterface $text_delivery;
    private I18NInterface $translator;

    public function __construct(
        TextDeliveryInterface $text_delivery,
        I18NInterface $translator
    ) {
        $this->text_delivery = $text_delivery;
        $this->translator = $translator;
    }

    public function print(string $message = '', array $placeholders = []): void
    {
        // ToDo: translate

        $this->text_delivery->output(
            $this->compileMessage($message, $placeholders)
        );
    }

    public function println(string $message = '', array $placeholders = []): void
    {
        $this->text_delivery->output($this->compileMessage($message, $placeholders) . PHP_EOL);
    }

    public function input(string $prompt = '', string $default = '', array $placeholders = []): string
    {
        $prompt = $this->compileMessage($prompt, $placeholders);

        $this->text_delivery->output($prompt);

        $value = $this->text_delivery->input();

        return $this->translator->findId($value);
    }

    private function compileMessage(string $message, array $placeholders): string
    {
        $message = $this->translator->translate($message);

        foreach ($placeholders as $placeholder => $value) {
            $value = $this->translate($value);
            $message = str_replace($placeholder, $value, $message);
        }

        return $message;
    }
}
