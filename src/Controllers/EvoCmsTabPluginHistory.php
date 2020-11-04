<?php namespace EvolutionCMS\EvocmsHistoryDoc\Controllers;

include_once(MODX_BASE_PATH . 'assets/lib/SimpleTab/plugin.class.php');

use EvolutionCMS\EvocmsHistoryDoc\Models\SiteContentHistory;
use EvolutionCMS\Facades\Console;
use Illuminate\Database\QueryException;
use League\Flysystem\Exception;
use \SimpleTab\Plugin;

class EvoCmsTabPluginHistory extends Plugin
{
    public $pluginName = 'HistoryPage';
    public $table = 'sg_images';
    public $tpl = 'core/vendor/ddaproduction/evocms-resourcehistory/chunks/history.tpl';
    public $tplRow = 'core/vendor/ddaproduction/evocms-resourcehistory/chunks/historyrow.tpl';
    public $tplTable = 'core/vendor/ddaproduction/evocms-resourcehistory/chunks/table.tpl';
    public $tplEmpty = 'core/vendor/ddaproduction/evocms-resourcehistory/chunks/table.tpl';
    public $tplHistoryButton = 'core/vendor/ddaproduction/evocms-resourcehistory/chunks/tplHistoryButton.tpl';

    /**
     * @return array
     */
    public function getTplPlaceholders()
    {
        $historyDoc = SiteContentHistory::query()->where('resource_id', $this->params['id'])->orderByDesc('id');
        $innerTable = '';
        $tplRow = file_get_contents(MODX_BASE_PATH . $this->tplRow);
        $tplTable = file_get_contents(MODX_BASE_PATH . $this->tplTable);
        $tplEmpty = file_get_contents(MODX_BASE_PATH . $this->tplEmpty);
        $tplHistoryButton = file_get_contents(MODX_BASE_PATH . $this->tplHistoryButton);
        if ($historyDoc->count() > 0) {
            $n = 0;
            foreach ($historyDoc->get()->toArray() as $row) {
                $row['n'] = ++$n;
                $row['pagetitle'] = json_decode($row['document_object'], true)['pagetitle'];
                $languages = config('app.blang.languages');
                if (!is_null($languages)) {
                    $row['historyButton'] = '';
                    foreach (explode('||', $languages) as $lang) {
                        $row_lang['history_link'] = '/'.$lang.\UrlProcessor::makeUrl($row['resource_id'], '', 'history_doc=' . $row['id']);
                        $row_lang['lang'] = $lang;
                        $row['historyButton']  .=  $this->DLTemplate->parseChunk('@CODE:' . $tplHistoryButton, $row_lang);
                    }
                } else {
                    $row['history_link'] = \UrlProcessor::makeUrl($row['resource_id'], '', 'history_doc=' . $row['id']);
                    $row['historyButton']  =  $this->DLTemplate->parseChunk('@CODE:' . $tplHistoryButton, $row);
                }
                $row['repair_link'] = '?check_repair=repair_'.md5($row['id'].$row['resource_id']).'&resource_id='.$row['resource_id'].'&doc_id='.$row['id'];
                $_SESSION['available_repair'][] = md5($row['id'].$row['resource_id']);
                $innerTable .= $this->DLTemplate->parseChunk('@CODE:' . $tplRow, $row);
            }
            $table = $this->DLTemplate->parseChunk('@CODE:' . $tplTable, ['innerTable' => $innerTable]);
        }else {
            $table = $tplEmpty;
        }
        $templates = trim(preg_replace('/,,+/', ',', preg_replace('/[^0-9,]+/', '', $this->params['templates'])), ',');
        $tpls = '[]';

        $ph = array(
            'tabName' => 'History',
            'inner' => $table,
            'lang' => $this->lang_attribute,
            'site_url' => $this->modx->config['site_url'],
            'manager_url' => MODX_MANAGER_URL,

            'refreshBtn' => (int)($_SESSION['mgrRole'] == 1),
            'tpls' => $tpls,

        );

        return array_merge($this->params, $ph);
    }


    public function checkTable()
    {
        try {
            $check_history = SiteContentHistory::first();
        } catch (Exception $e) {
            return false;
        } catch (QueryException $exception) {
            return false;
        }

        if (is_null($check_history)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return mixed
     */
    public function createTable()
    {
        Console::call('migrate', ['--path' => 'vendor/ddaproduction/evocms-resourcehistory/migrations/', '--force' => true]);
        return true;
    }
}
