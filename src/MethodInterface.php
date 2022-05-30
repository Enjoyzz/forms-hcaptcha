<?php

declare(strict_types=1);


namespace Enjoys\Forms\Captcha\hCaptcha;


interface MethodInterface
{
    public function render(): string;
}
