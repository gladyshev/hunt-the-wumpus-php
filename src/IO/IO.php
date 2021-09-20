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

    public function print(string $messageId = '', array $placeholders = []): void
    {
        $this->textDelivery->output(
            $this->compileMessage($messageId, $placeholders)
        );
    }

    public function println(string $messageId = '', array $placeholders = []): void
    {
        $this->textDelivery->output($this->compileMessage($messageId, $placeholders) . PHP_EOL);
    }

    public function inputln(string $promptMessageId = '', string $default = '', array $placeholders = []): string
    {
        $promptMessageId = $this->compileMessage($promptMessageId, $placeholders);

        $this->textDelivery->output($promptMessageId);

        $value = $this->textDelivery->input();

        $value = strtoupper(trim($value));

        return $this->translator->findId($value);
    }

    private function compileMessage(string $message, array $placeholders): string
    {
        $message = $this->translator->translate($message);

        foreach ($placeholders as $placeholder => $value) {
            $value = $this->translator->translate($value);
            $message = str_replace('{' . $placeholder . '}', $value, $message);
        }

        return $message;
    }
}
