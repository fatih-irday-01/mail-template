<?php

namespace Fatihirday\MailTemplate;

use Fatihirday\MailTemplate\Models\MailLanguage;
use Fatihirday\MailTemplate\Models\MailTemplate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;

trait MailViewTemplate
{
    /**
     * @param string $view
     * @param string $languageCode
     * @return mixed
     */
    public function builder(string $view, string $languageCode)
    {
        $template = $this->getTemplate($languageCode, $view);

        $data = !empty($this->data) ? $this->data : [];

        $data = array_merge(['isRtl' => isRtlLang($languageCode)], $data);

        $html = Blade::render(
            $template->body,
            $data
        );

        return $this->subject($template->subject)->html($html);
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

        $cacheKey = sprintf(config('mail-template.cache_vars.key'), $view, $languageCode);

        return Cache::remember(
            $cacheKey,
            config('mail-template.cache_vars.expires_at'),
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
        $template = MailTemplate::query()->getView($view);

        $subjectKey = sprintf(
            '{%s.%s.subject}',
            config('mail-template.start_suffix_subject'),
            $view
        );

        $template->subject = $this->changeStaticKey($subjectKey, $languageCode);
        $template->body = $this->changeStaticKey($template->body, $languageCode);

        return $template;
    }

    /**
     * @param string $viewBody
     * @param string $languagecode
     * @return string
     */
    protected function changeStaticKey(string $viewBody, string $languagecode): string
    {
        $start = sprintf('%s.%%', config('mail-template.start_suffix_subject'));

        $statics = MailLanguage::query()
            ->where('language_code', $languagecode)
            ->where('key', 'LIKE', $start)
            ->get()
            ->mapWithKeys(fn($item) => [sprintf('{%s}', $item->key) => $item->value])
            ->toArray();

        return strtr($viewBody, $statics);
    }
}
