<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\IO;

use Htw\I18N\I18NInterface;
use Htw\TextDelivery\TextDeliveryInterface;

final class IO implements \Htw\IO\IOInterface
{
    private TextDeliveryInterface $textDelivery;
    private I18NInterface $translator;

    public function __construct(
        TextDeliveryInterface $text_delivery,
        I18NInterface $translator
    ) {
        $this->textDelivery = $text_delivery;
        $this->translator = $translator;
    }

    public function print(string $message = '', array $placeholders = []): void
    {
        // ToDo: translate

        $this->textDelivery->output(
            $this->compileMessage($message, $placeholders)
        );
    }

    public function println(string $message = '', array $placeholders = []): void
    {
        $this->textDelivery->output($this->compileMessage($message, $placeholders) . PHP_EOL);
    }

    public function input(string $prompt = '', string $default = '', array $placeholders = []): string
    {
        $prompt = $this->compileMessage($prompt, $placeholders);

        $this->textDelivery->output($prompt);

        $value = $this->textDelivery->input();

        return $this->translator->findId($value);
    }

    private function compileMessage(string $message, array $placeholders): string
    {
        $message = $this->translator->translate($message);

        foreach ($placeholders as $placeholder => $value) {

            $message = str_replace('{' . $placeholder . '}', $value, $message);
        }

        return $message;
    }
}
