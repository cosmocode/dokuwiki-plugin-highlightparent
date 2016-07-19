<?php
/**
 * DokuWiki Plugin highlightparent (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <dokuwiki@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_highlightparent extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {

       $controller->register_hook('TPL_CONTENT_DISPLAY', 'BEFORE', $this, 'handle_tpl_content_display');

    }

    /**
     * [Custom event handler which performs action]
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */

    public function handle_tpl_content_display(Doku_Event &$event, $param) {
        global $ID, $ACT;
        if ($ACT != 'show') {
            return;
        }
        $pattern = trim($this->getConf('namespace pattern'));
        if ($pattern == '') {
            return;
        }

        $matches = array();

        if (preg_match('/' . $pattern . '/', $ID, $matches) == 1) {
            global $conf;
            $baseID=$matches[1];
            if (substr($baseID, -1) == ':') {
                $baseID .= $conf['start'];
            }
            if ($baseID == $ID) {
                return;
            }
            $baseTitle = p_get_first_heading($baseID);
            $xhtml_renderer = new Doku_Renderer_xhtml();
            $link = $xhtml_renderer->internallink($baseID, ($baseTitle ? $baseTitle : $baseID), false, true);
            $link = "<span id='plugin__highlightparent'>$link</span>";
            $event->data = $link . $event->data;
        }
    }

}

// vim:ts=4:sw=4:et:
