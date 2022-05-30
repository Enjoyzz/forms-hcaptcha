<?php

declare(strict_types=1);

namespace Enjoys\Forms\Captcha\hCaptcha;


use Enjoys\Forms\Element;
use Enjoys\Forms\Elements\Captcha;
use Enjoys\Forms\Interfaces\CaptchaInterface;
use Enjoys\Forms\Interfaces\Ruleable;
use Enjoys\Forms\Traits\Request;
use Enjoys\Traits\Options;
use GuzzleHttp\Client;

class hCaptcha implements CaptchaInterface
{
    use Options;
    use Request;

    private const VERIFY_URL = 'https://hcaptcha.com/siteverify';

    /**
     * @var class-string<MethodInterface>
     */
    private string $method = Standart::class;

    private ?string $ruleMessage = null;

    private string $language = 'en';


    public function __construct(string $siteKey, string $secret)
    {
        $this->setOption('siteKey', $siteKey);
        $this->setOption('secret', $secret);
    }

    public function getName(): string
    {
        return 'h-captcha';
    }

    public function getRuleMessage(): ?string
    {
        return $this->ruleMessage;
    }

    public function setRuleMessage(?string $message = null): void
    {
        $this->ruleMessage = $message;
    }


    public function renderHtml(Element $element): string
    {
        return (new $this->method($element))->render();
    }

    public function validate(Ruleable $element): bool
    {
        $client = $this->getOption('httpClient', $this->getGuzzleClient());

        $data = [
            'secret' => $this->getOption('secret'),
            'response' => $this->getRequest()->getPostData(
                'h-captcha-response',
                $this->getRequest()->getQueryData('h-captcha-response')
            )
        ];

        $response = $client->request('POST', self::VERIFY_URL, [
            'form_params' => $data
        ]);

        $responseBody = \json_decode($response->getBody()->getContents());


        if ($responseBody->success === false) {
            $errors = [];
            foreach ($responseBody->{'error-codes'} as $error) {
                $errors[] = $this->getErrorCode($error);
            }
            /** @psalm-suppress UndefinedMethod */
            $element->setRuleError(implode(', ', $errors));
            return false;
        }
        return true;
    }

    /**
     * Used across setOption()
     * @param string $lang
     * @return void
     */
    public function setLanguage(string $lang): void
    {
        $this->language = \strtolower($lang);
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     *
     * @return Client
     */
    private function getGuzzleClient(): Client
    {
        return new Client();
    }


    public function getMethod(): string
    {
        return $this->method;
    }


    /**
     * @param class-string<MethodInterface> $method
     * @return void
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return string[]
     */
    public function getErrorCodes(): array
    {
        $file_language = __DIR__ . '/Language/' . $this->getLanguage() . '.php';

        if (file_exists($file_language)) {
            return include $file_language;
        }

        return include __DIR__ . '/Language/en.php';
    }

    public function getErrorCode(string $code): string
    {
        $errorCodes = $this->getErrorCodes();
        return $errorCodes[$code] ?? '';
    }


}
