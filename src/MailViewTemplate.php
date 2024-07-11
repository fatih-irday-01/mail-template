<?php

namespace Fatihirday\MailTemplate;

use Fatihirday\MailTemplate\Models\MailLanguage;
use Fatihirday\MailTemplate\Models\MailTemplate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait MailViewTemplate
{
    /**
     * @param string $view
     * @param string $languageCode
     * @param string|null $customSubject
     * @return \App\Mail\ExampleMail|MailViewTemplate
     */
    public function builder(string $view, string $languageCode, ?string $customSubject = null): self
    {
        $template = $this->getTemplate($languageCode, $view);

        $data = !empty($this->data) ? $this->data : [];

        $data = array_merge([
            'isRtl' => isRtlLang($languageCode),
            'languageCode' => $languageCode,
            'baseUrl' => config('app.url'),
        ], $data);

        $html = Blade::render($template->body, $data);

        if (str_contains($html, (config('mail-template.start_suffix_subject')))) {
            $html = $this->changeStaticKey($html, $languageCode);
        }

        return $this->subject($customSubject ?: $template->subject)->html($html);
    }

    /**
     * @param string $languageCode
     * @param string $view
     * @return MailTemplate
     */
    protected function getTemplate(string $languageCode, string $view): MailTemplate
    {
        if (!config('mail-template.cache')) {
            return $this->setTemplate($view, $languageCode);
        }

        $cacheKey = sprintf('mail:template:%s:%s', $view, $languageCode);

        return Cache::tags('mail_template')->remember(
            $cacheKey,
            config('mail-template.cache_expires_at'),
            function () use ($view, $languageCode) {
                return $this->setTemplate($view, $languageCode);
            }
        );
    }

    /**
     * @param string $view
     * @param string $languageCode
     * @return MailTemplate
     */
    protected function setTemplate(string $view, string $languageCode): MailTemplate
    {
        $template = MailTemplate::query()->getView($view)->first();

        if (empty($template)) {
            Log::error('Not found mail template : ' . $view);
            exit();
        }

        $subjectKey = sprintf(
            '{%s.%s.subject}',
            config('mail-template.start_suffix_subject'),
            $view
        );

        $template->subject = $this->getChangeStaticKey($view . ':subject', $subjectKey, $languageCode);
        $template->body = $this->getChangeStaticKey($view . ':body', $template->body, $languageCode);

        return $template;
    }

    /**
     * @param string $view
     * @param string $viewBody
     * @param string $languageCode
     * @return string|null
     */
    protected function getChangeStaticKey(string $view, string $viewBody, string $languageCode): ?string
    {
        if (!config('mail-template.cache')) {
            return $this->changeStaticKey($viewBody, $languageCode);
        }

        $cacheKey = sprintf('mail:language:%s:%s', $view, $languageCode);

        return Cache::tags('mail_template')->remember(
            $cacheKey,
            config('mail-template.cache_expires_at'),
            function () use ($view, $viewBody, $languageCode) {
                return $this->changeStaticKey($viewBody, $languageCode);
            }
        );
    }

    /**
     * @param string $viewBody
     * @param string $languageCode
     * @return string|null
     */
    protected function changeStaticKey(string $viewBody, string $languageCode): ?string
    {
        $start = sprintf('%s.%%', config('mail-template.start_suffix_subject'));

        $statics = MailLanguage::query()
            ->where('language_code', $languageCode)
            ->where('key', 'LIKE', $start)
            ->get()
            ->mapWithKeys(fn($item) => [sprintf('{%s}', $item->key) => $item->value])
            ->toArray();

        return strtr($viewBody, $statics);
    }
}
