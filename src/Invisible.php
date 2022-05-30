<?php

declare(strict_types=1);


namespace Enjoys\Forms\Captcha\hCaptcha;


use Enjoys\Forms\AttributeFactory;
use Enjoys\Forms\Elements\Captcha;

class Invisible extends Standart
{
    public function __construct(Captcha $element)
    {
        parent::__construct($element);
        $this->setOption('data-callback', 'onSubmit');
    }

    public function render(): string
    {
        $form = $this->getElement()->getForm();

        if ('' === $formAttributeId = $form->getAttribute('id')?->getValueString()) {
            throw new \InvalidArgumentException('Set attribute form id');
        }



        $submitElement = $form->getElement($this->getElement()->getCaptcha()->getOption('submitEl', 'submit'));

        if ($submitElement === null) {
            throw new \InvalidArgumentException('Set correctly submit element name. Option is `submitEl`');
        }

        if ($submitElement->getAttribute('id')->getValueString() === 'submit') {
            throw new \InvalidArgumentException(
                'The submit button ID should not be `submit`, please set a different id for the submit button'
            );
        }

        $submitElement->addAttributes(
            AttributeFactory::createFromArray($this->getAttributes())
        );

        return sprintf(
            <<<HTML
<script src="%s" async defer></script>
 <script>
   function onSubmit(token) {
     document.getElementById("%s").submit();
   }
 </script>
HTML,
            $this->getOption('h-captcha-script-url', 'https://js.hcaptcha.com/1/api.js'),
            $formAttributeId
        );
    }
}
