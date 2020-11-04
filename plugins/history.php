<?php

Event::listen('evolution.OnDocFormSave', function ($params) {
    if (!isset($_GET['check_repair'])) {
        $modx = EvolutionCMS();
        if (is_numeric($params['id']) && $params['id'] > 0) {
            $docObj = $modx->makeDocumentObject($params['id']);
            \EvolutionCMS\EvocmsHistoryDoc\Models\SiteContentHistory::create(['resource_id' => $params['id'], 'document_object' => json_encode($docObj, JSON_UNESCAPED_UNICODE), 'post_data' => json_encode($_POST, JSON_UNESCAPED_UNICODE)]);
        }
    }
});

Event::listen('evolution.OnManagerPageInit', function ($params) {

    if (isset($_GET['check_repair']) && stristr($_GET['check_repair'], 'repair_') && isset($_GET['resource_id']) && isset($_GET['doc_id'])) {

        $check = str_replace('repair_', '', $_GET['check_repair']);

        if (in_array($check, $_SESSION['available_repair'])) {
            $prePost = \EvolutionCMS\EvocmsHistoryDoc\Models\SiteContentHistory::find($_GET['doc_id'])->post_data;
            $_POST = json_decode($prePost, true);
            if($_POST['id'] == 0) {
                $_POST['id'] = $_GET['resource_id'];
                $_POST['mode'] = 27;
            }
            $_REQUEST['parent'] = $_POST['parent'];
            $_REQUEST['id'] = $_POST['id'];
            define('IN_MANAGER_MODE', true);

            include MODX_MANAGER_PATH . 'processors/save_content.processor.php';
        }

    }
});

Event::listen('evolution.OnAfterLoadDocumentObject', function ($params) {
    if (isset($_GET['history_doc'])) {
        return json_decode(\EvolutionCMS\EvocmsHistoryDoc\Models\SiteContentHistory::find($_GET['history_doc'])->document_object, true);
    }

});

Event::listen('evolution.OnDocFormRender', function ($params) {
    $modx = EvolutionCMS();
    $modx->event->params = $params;
    $modx->event->params['templates'] = $modx->getConfig('docHistoryTemplateConfig');

    global $richtexteditorIds;
    //Hack to check if TinyMCE scripts are loaded
    if (isset($richtexteditorIds['TinyMCE4'])) {
        $modx->loadedjscripts['TinyMCE4'] = array('version' => '4.3.6');
    }
    $plugin = new \EvolutionCMS\EvocmsHistoryDoc\Controllers\EvoCmsTabPluginHistory($modx, $modx->getConfig('lang_code'));
    if ($params['id']) {
        $output = $plugin->render();

    } else {
        $output = $plugin->renderEmpty();
    }
    if ($output) {
        //$modx->event->addOutput($output);
        return $output;
    }
});
