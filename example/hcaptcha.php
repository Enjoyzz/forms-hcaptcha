<?php

declare(strict_types=1);

use Enjoys\Forms\Captcha\reCaptcha\reCaptcha;
use Enjoys\Forms\Form;
use Enjoys\Forms\Renderer\Html\HtmlRenderer;

require __DIR__ . '/../vendor/autoload.php';

try {
    $form = new Form();
    $captcha = new \Enjoys\Forms\Captcha\hCaptcha\hCaptcha(
        '0b196429-b5cd-48b5-a1cb-d6559038cb8a',
        '0x68f7631b7c68e6aB76DcC7AFdec96ca53aC67f30',
    );
    $captcha->setMethod(\Enjoys\Forms\Captcha\hCaptcha\Standart::class);



    $form->captcha($captcha);
    $form->submit('submit');
    if ($form->isSubmitted()) {
        dump($_REQUEST);
    }
    $renderer = new HtmlRenderer($form);
    echo include __DIR__ . '/.assets.php';
    echo sprintf('<div class="container-fluid">%s</div>', $renderer->output());
} catch (Exception|Error $e) {
    echo 'Error: ' . $e->getMessage();
}
