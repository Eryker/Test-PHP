<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {

        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote)
        {
            $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);

            if(strpos($text, '[quote:destination_link]') !== false){
                $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
            }

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary     = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($_quoteFromRepository),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($_quoteFromRepository),
                        $text
                    );
                }
            }

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]',$destinationOfQuote->countryName,$text);
        }

        if (isset($destination))
            $text = str_replace('[quote:destination_link]', $usefulObject->url . '/' . $destination->countryName . '/quote/' . $_quoteFromRepository->id, $text);
        else
            $text = str_replace('[quote:destination_link]', '', $text);

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $APPLICATION_CONTEXT->getCurrentUser();
        if($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]'       , ucfirst(mb_strtolower($_user->firstname)), $text);
        }

        return $text;
    }

    private function QuoteReplacement($text, array $data, array $replacements)
    $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote)
        {

            $quoteRepo  = QuoteRepository::getInstance()->getById($quote->id);
            $site           = SiteRepository::getInstance()->getById($quote->siteId);
            $destination    = DestinationRepository::getInstance()->getById($quote->destinationId);

            // ----- QUOTE PLACEHOLDERS TO BE REPLACED -----

            if(strpos($text, '[quote:destination_name]'))
                $replacements['[quote:destination_name]'] = $destination->countryName;

            if(strpos($text, '[quote:destination_link]'))
                $replacements['[quote:destination_link]'] = $destination ? $site->url . '/' . $destination->countryName . '/quote/' . $quoteFromRepo->id : '';

            if(strpos($text, '[quote:summary_html]'))
                $replacements['[quote:summary_html]'] = Quote::renderHtml($quoteFromRepo);

            if(strpos($text, '[quote:summary]'))
                $replacements['[quote:summary]'] = Quote::renderText($quoteFromRepo);




            // -----

        }

        return $replacements;
    }


    private function UserReplacement($text, array $data, array $replacements)
    {

        $appContext = ApplicationContext::getInstance();

        $user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $appContext->getCurrentUser();

        // ----- USER PLACEHOLDERS TO BE REPLACED -----

        if(strpos($text, '[user:first_name]'))


        // -----

        return $replacements;
    }
}
