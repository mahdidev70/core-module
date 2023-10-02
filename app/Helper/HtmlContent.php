<?php

namespace TechStudio\Core\app\Helper;

use TechStudio\Blog\app\Models\Article;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class HtmlContent {

    /**
     * Processes the HTML content from the old seller academy website so that
     * it can be correctly displayed in the new website.
     */
    public static function normalizeWordpressContent(string $content) {
        libxml_use_internal_errors(true);
        $dom = new \DomDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        // unwrap images lazy load (modern browsers support native <img> lazy load)
        foreach ($dom->getElementsByTagName('img') as $item) {
            $lazySrc = $item->getAttribute('data-lazy-src');
            if ($lazySrc) {
                $item->setAttribute('src', $lazySrc);
                $item->removeAttribute('data-lazy-src');
            }
        }
        // unwrap dynamically highlighted text
        $finder = new \DomXPath($dom);
        $highlighted_divs = $finder->query("//div[contains(@class, 'elementor-headline--style-highlight')]");
        if ($highlighted_divs) {
            foreach($highlighted_divs as $div) {
                $settings_object = json_decode($div->getAttribute('data-settings'));
                $heading_element = $finder->query("descendant::span[contains(@class, 'elementor-headline-dynamic-wrapper')]", $div);
                if (count($heading_element) != 1) {
                    continue;
                }
                $heading_element->item(0)->textContent .= $settings_object->highlighted_text;
            }
        }
        // prefer internal links TODO: merge DB calls
        $hrefs = $finder->query("//a[contains(@href, 'selleracademy.digikala.com')]");
        if ($hrefs) {
            foreach ($hrefs as $a) {
                $link = $a->getAttribute('href');
                $path_items = explode('/', $link);
                $slug = end($path_items);
                if (!$slug) {
                    $slug = prev($path_items);
                }
                $lookup = Article::where('slug', $slug)->where('status', 'published')->get();
                if ($lookup) {
                    $a->setAttribute('href', "https://sa-test.techstudio.diginext.ir/article/$slug");
                }
            }
        }
        return html_entity_decode($dom->saveHTML());
    }

    /**
     * Uses the first <p> tag in the content to generate summary.
     */
    public static function autoGenerateSummary(string $content) {
        libxml_use_internal_errors(true);
        $dom = new \DomDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
        if (!$dom) {
            echo "Failed to parse html content";
            return;
        }
        $finder = new \DomXPath($dom);

        $p = $finder->query('//p');

        if (!$p || count($p) == 0 ) {
            return 'Cannot generate summary -- No <p> tag';
        }
        $summary = trim($p[0]->textContent);
        return $summary;
    }

    /**
     * Removes any unsafe tags from the HTML content.
     */
    public static function sanitizeHtml(string $content) {
        $config = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            // ->allowStaticElements()  // unsafe
            ->allowRelativeMedias()
            ->withMaxInputLength(1_000_000)
        ;

        $sanitizer = new HtmlSanitizer($config);
        $content = $sanitizer->sanitize($content);
        return $content;
    }

    /**
     * Returns a list of all the image urls in the article
     */
    // public static function getImageUrls(string $content) {
    //     $urls = [];
    //     preg_match_all("(https?:\/\/.*\.(?:png|jpg))", $content, $urls);
    //     $urls = array_map(fn ($url) => $url[0], $urls);
    //     return $urls;
    // }

    /**
     * Returns a list of all the image urls in the article
     */
    public static function getImageUrls(string $content) {
        libxml_use_internal_errors(true);
        $dom = new \DomDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
        if (!$dom) {
            echo "Failed to parse html content";
            return;
        }
        $finder = new \DomXPath($dom);

        $imgs = $finder->query('//img');
        $urls = array_map(fn ($img) => $img->getAttribute('src'), iterator_to_array($imgs) ?: []);
        return $urls;
    }

    public static function minutesToRead(string $content) {
        return max(1, round(strlen($content) / 5.4 / 225 * 0.7));  // 5.4 characters per word, 225 words per minute, 70% of the content is text
    }

    public static function cutFirstAndGetElement($dom) {    
        $finder = new \DomXPath($dom);
        $img = $finder->query('(//img)[1]');
        if (!$img || count($img) == 0) {
            return [$dom, null, null];
        }

        $dom_after = clone $dom;
        $finder = new \DomXPath($dom_after);
        $after = $finder->query('(//img)[1]/preceding::*');
        foreach ($after as $item) {
            $item->parentNode->removeChild($item);
        }
        $item = $finder->query('(//img)[1]')[0];
        $item->parentNode->removeChild($item);

        $dom_before = clone $dom;
        $finder = new \DomXPath($dom_before);
        $before = $finder->query('(//img)[1]/following::*');
        foreach ($before as $item) {
            $item->parentNode->removeChild($item);
        }
        $item = $finder->query('(//img)[1]')[0];
        $item->parentNode->removeChild($item);

        return [$dom_before, $img[0], $dom_after];
    }

    public static function htmlToBlocks($html) {
        $blocks = [];
        libxml_use_internal_errors(true);
        $dom = new \DomDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        while (true) {
            [$before, $element, $dom] = HtmlContent::cutFirstAndGetElement($dom);
            $blocks[] = [
                'type' => 'html',
                'content' => [
                    'props' => '',
                    'model' => $before->saveHtml(),
                ],
            ];
            if (!$element) {
                break;
            }
            $blocks[] = [
                'type' => 'img',
                'content' => [
                    'props' => '',
                    'url' => $element->getAttribute('src'),
                    'align' => 'center',
                    'caption' => '',
                ],
            ];
        }
        return $blocks;
    }

    // private static function blox(html) {
    //     for (element in html) {
    //         if (has_chidren(element)) {
    //             b = blox(children(element));
    //             if 
    //         }
    //     }
    // }

}
