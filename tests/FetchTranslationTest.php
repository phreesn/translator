<?php
namespace Nyrados\Translator\Tests;

use Nyrados\Translator\Language\Language;
use Nyrados\Translator\Provider\ArrayProvider;
use Nyrados\Translator\TranslatorApi;
use PHPUnit\Framework\TestCase;

class FetchTranslationTest extends TestCase
{

    /** @var TranslatorApi */
    private $translator;

    protected function setUp(): void
    {
        $this->translator = new TranslatorApi();
        $this->translator->addProvider($provider = new ArrayProvider());
        $this->translator->setPreferences(['es', 'en-us']);

        $provider->set('en', [
            'test1' => 'test1_en-en',
            'test2' => 'test2_en-en',
            'test3' => 'test3_en-en'
        ]); 
        
        $provider->set('en-us', [
            'test1' => 'test1_en-us',
            'test2' => 'test2_en-us'
        ]);

        $provider->set('es', [
            'test1' => 'test1_es-es'
        ]);
    }

    /** SINGLE */

    public function testCanFetchSingleTranslation()
    {
        $translation = $this->translator->fetchTranslations(['test1'])['test1'];

        $this->assertSame($translation->getLanguage()->getId(), 'es-es');
        $this->assertSame((string) $translation, 'test1_es-es');
    }

    public function testCanFetchNextPreference()
    {
        $translation = $this->translator->fetchTranslations(['test2'])['test2'];

        $this->assertSame($translation->getLanguage()->getId(), 'en-us');
        $this->assertSame((string) $translation, 'test2_en-us');
    }

    public function testCanFetchByIgnoreRegion()
    {
        $translation = $this->translator->fetchTranslations(['test3'])['test3'];

        $this->assertSame($translation->getLanguage()->getId(), 'en-en');
        $this->assertSame((string) $translation, 'test3_en-en');
    }

    public function testInvalidLanguageString()
    {
        $this->assertEmpty($this->translator->fetchTranslations(['test4']));
    }

    /** MULTIPLE */

    public function testCanFetchMultiple()
    {
        $lang = 'en-us';
        foreach ($this->translator->fetchTranslations(['test1', 'test2']) as $string => $translation) {

            $this->assertSame($lang, $translation->getLanguage()->getId());
            $this->assertSame($string . '_' . $translation->getLanguage()->getId(), (string) $translation);

        }

        $lang = 'en-en';
        foreach ($this->translator->fetchTranslations(['test1', 'test2', 'test3']) as $string => $translation) {
            $this->assertSame($lang, $translation->getLanguage()->getId());
            $this->assertSame($string . '_' . $translation->getLanguage()->getId(), (string) $translation);
        }
    }




}