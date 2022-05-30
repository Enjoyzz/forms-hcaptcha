<?php

declare(strict_types=1);


namespace Enjoys\Forms\Captcha\hCaptcha;


use Enjoys\Forms\Elements\Captcha;
use Enjoys\Traits\Options;

class Standart
{
    use Options;

    public function __construct(private Captcha $element)
    {
        $this->setOptions($element->getCaptcha()->getOptions());
    }

    protected function getAttributes(): array
    {
        return array_filter([
            'class' => 'h-captcha',
            'data-sitekey' => $this->getOption('siteKey'),
            'data-theme' => $this->getOption('data-theme'),
            'data-size' => $this->getOption('data-size'),
            'data-tabindex' => $this->getOption('data-tabindex'),
            'data-callback' => $this->getOption('data-callback'),
            'data-expired-callback' => $this->getOption('data-expired-callback'),
            'data-error-callback' => $this->getOption('data-error-callback'),
        ], function ($value) {
            return $value !== null;
        });
    }

    public function getElement(): Captcha
    {
        return $this->element;
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();

        return sprintf(
            <<<HTML
<script src="%s" async defer></script>
<div %s></div>
HTML,
            $this->getOption('h-captcha-script-url', 'https://js.hcaptcha.com/1/api.js'),
            implode(
                ' ',
                array_map(function ($attribute, $value) {
                    return sprintf('%s="%s"', $attribute, $value);
                }, array_keys($attributes), array_values($attributes))
            )
        );
    }
}
