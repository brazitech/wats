<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

wimport('helper.alias');
jimport('joomla.utilities.date');

/**
 *
 * @todo
 */
class WKnowledgeHelper {

    public static $domain;

	public function parse($domain, $content, $params=null) {
        WKnowledgeHelper::$domain = $domain;

        // split content into parts based on code
        $pattern = '~(\<nocode\>|\<\/nocode>)~';
        $content = preg_split($pattern,
                              $content,
                              -1,
                              PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // itterate over the pieces, assume code is allowed at the start
        $nocode = false;
        for ($i = 0, $c = count($content); $i < $c; $i++) {
            if ($content[$i] == '<nocode>') {
                // change flag status
                $nocode = true;
            } elseif ($content[$i] == '</nocode>') {
                // change flag status
                $nocode = false;
            } elseif (!$nocode) {
                // section of content allows code, parse away!
                $content[$i] = WKnowledgeHelper::addInternalLinks($content[$i], $params);
                $content[$i] = WKnowledgeHelper::addInternalFragmentLinks($content[$i], $params);
                $content[$i] = WKnowledgeHelper::addExternalLinks($content[$i], $params);
            }
        }

        // reasemble the content
        return implode($content);

	}

    public function getSections($content, $params) {
        $matches  = array();
        $sections = array();
        //$pattern  = '~\<h([1-9])\>\=(.+)\<\/h[1-9]>~';
        $pattern  = '~\<h([1-3])\>(.+)\<\/h[1-3]>~i';
        
        preg_match_all($pattern, $content, $matches);

        for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
            switch ($matches[1][$i]) {
                case '1':
                    $sections[] = array(
                        'section' => $matches[2][$i],
                        'children' => array()
                    );
                    break;
                case '2':
                    $sections[count($sections) - 1]['children'][] = array(
                        'section' => $matches[2][$i],
                        'children' => array()
                    );
                    break;
                case '3':
                    $sections[count($sections) - 1]
                             ['children']
                             [count($sections[count($sections) - 1]['children']) - 1]
                             ['children'][] = array(
                                 'section' => $matches[2][$i]
                             );
            }
        }

        // all done!
        return $sections;
    }

    /**
     *
     * @param string $content
     * @param JParameter $params
     * @return <type> 
     */
    private function addInternalLinks($content, $params) {
        $pattern = '~\[\['                          // two opening square braces
                 . '(([a-z\ \-\_\.]+)\:)?'          // optinal knowledge domain
                 . '([a-z\ \-\_\.]+)'               // link
                 . '(\#[a-z\-\_\.]+)?'            // optional anchor
                 . '(\|(.+))?'                      // optional alias
                 . '\]\]'                           // two closing square braces
                 . '([a-z]*)'                       // blending
                 . '~i';                            // case insenitive modifier

        return preg_replace_callback($pattern, 'WKnowledgeHelper::addInternalLink', $content);
    }

    /**
     * Callback method used by addInternalLinks() to create individual internal
     * links.
     *
     * @see WKnowledgeHelper::addInternalLinks()
     * @param string[] $matches
     * @return string
     */
    public static function addInternalLink($matches) {
        $db = JFactory::getDBO();

        // get the data so we can work out what's going on
        $domain     = $matches[2];
        $alias      = WAliasHelper::buildAlias($matches[3]);
        $linkAnchor = $matches[4];
        $linkAlias  = ($matches[6] != '') ? $matches[6] : $matches[3];
        $blend      = $matches[7];

        $sql = 'SELECT ' . dbName('k.id') . ', COUNT(' . dbName('r.revision') . ') AS ' . dbName('latestRevision')
             . ' FROM ' . dbTable('knowledge') . ' AS ' . dbName('k')
             . ' LEFT JOIN ' . dbTable('knowledge_revision') . ' AS ' . dbName('r')
             . ' ON ' .dbName('r.knowledge') . ' = ' . dbName('k.id')
             . ' WHERE ' . dbName('alias') . ' = ' . $db->Quote($alias)
             . ' GROUP BY ' . dbName('k.id');
        $db->setQuery($sql);
        $linkTo = $db->loadObject();

        if (!$linkTo) {
            $date  = new JDate();
            $sql = 'INSERT INTO ' . dbTable('knowledge')
                 . ' SET ' . dbName('name') . ' = ' . $db->Quote($alias)
                 . ', ' . dbName('alias') . ' = ' . $db->Quote($alias)
                 . ', ' . dbName('domain') . ' = ' . $db->Quote(WKnowledgeHelper::$domain)
                 . ', ' . dbName('created') . ' = ' . $db->Quote($date->toMySQL())
                 . ', ' . dbName('created_by') . ' = ' . $db->Quote(JFactory::getUser()->get('id'));
            $db->setQuery($sql);
            $db->query();

            $linkTo = new stdClass();
            $linkTo->latestRevision = 0;
            $linkTo->id = $db->insertid();
        }

        $uri = JRoute::_('index.php?option=com_whelpdesk&task=knowledge.display&id='.$linkTo->id);

        return '<a href="' . $uri . $linkAnchor . '">'
             . ((!$linkTo->latestRevision) ? '<span style="color: red;">' : '')
             . $linkAlias
             . $blend
             . ((!$linkTo->latestRevision) ? '</span>' : '')
             . '</a>';
    }

    private function addInternalFragmentLinks($content, $params) {
        $pattern = '~\[\['                          // two opening square braces
                 . '(\#([a-z\-\ \_\.]+))'               // anchor
                 . '(\|(.+))?'                      // optional alias
                 . '\]\]'                           // two closing square braces
                 . '([a-z]*)'                       // blending
                 . '~i';                            // case insenitive modifier

        return preg_replace_callback($pattern, 'WKnowledgeHelper::addInternalFragmentLink', $content);
    }

    public static function addInternalFragmentLink($matches) {
        $db = JFactory::getDBO();

        // get the data so we can work out what's going on
        $linkAnchor = $matches[1];
        $linkAlias  = ($matches[4] != '') ? $matches[4] : $matches[2];
        $blend      = $matches[5];

        return '<a href="' . $linkAnchor . '">'
             . $linkAlias
             . $blend
             . '</a>';
    }

    private function addExternalLinks($content, $params) {
        $pattern = '~\['                            // single opening square brace
                 . '(\b(http|https|ftp)'            // protocol
			     . '[^][<>"\\x00-\\x20\\x7F]+)'     // link
                 . '\s*([^\]\\x0a\\x0d]*?)\]'       // optional link alias
                 . '~S';                            // single closing square brace

        return preg_replace_callback($pattern, 'WKnowledgeHelper::addExternalLink', $content);
    }

    public static function addExternalLink($matches) {
        $db = JFactory::getDBO();

        // get the data so we can work out what's going on
        $uri        = $matches[1];
        $linkAlias  = ($matches[3] != '') ? $matches[3] : $uri;

        return '<a href="' . $uri . '">' . $linkAlias . '</a>';
    }

}
