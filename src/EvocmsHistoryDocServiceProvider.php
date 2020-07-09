<?php namespace EvolutionCMS\EvocmsHistoryDoc;

use EvolutionCMS\ServiceProvider;

class EvocmsHistoryDocServiceProvider extends ServiceProvider
{
    /**
     * Если указать пустую строку, то сниппеты и чанки будут иметь привычное нам именование
     * Допустим, файл test создаст чанк/сниппет с именем test
     * Если же указан namespace то файл test создаст чанк/сниппет с именем evocmsHistoryDoc#test
     * При этом поддерживаются файлы в подпапках. Т.е. файл test из папки subdir создаст элемент с именем subdir/test
     */
    protected $namespace = 'evocmsHistoryDoc';
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        /*$this->loadSnippetsFrom(
            dirname(__DIR__). '/snippets/',
            $this->namespace
        );*/
        /*$this->loadChunksFrom(
            dirname(__DIR__) . '/chunks/',
            $this->namespace
        );*/
        $this->loadPluginsFrom(
            dirname(__DIR__) . '/plugins/'
        );
        //use this code for each module what you want add
        /*$this->app->registerModule(
            'module from file',
            dirname(__DIR__).'/modules/module.php'
        );*/
    }
}