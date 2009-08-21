<?php

jimport('joomla.filesystem.folder');


abstract class WContentHelper {
    public function save($type, $identifier, $content) {
        // get ready
        $contentHelpers = self::getContentHelpers();
        $params = array(
            $type,
            $identifier,
            $content
        );

        // itterate over helpers and execute functions if they exist
        foreach ($contentHelpers as $helper) {
            $functionName = 'wcontent'.$helper.'HelperSave';
            if (function_exists($functionName)) {
                call_user_func_array($functionName, $params);
            }
        }
    }

    public function update() {
        $contentHelpers = self::getContentHelpers();
    }

    public function delete() {
        $contentHelpers = self::getContentHelpers();
    }

    private static $contentHelpers;

    private function getContentHelpers() {
        // import content helpers if we need to
        if (!self::$contentHelpers) {
            $files = JFolder::files(
                JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'helper' . DS . 'content',
                'content\_[a-z]+\.php'
            );
            $contentHelpers = array();
            foreach ($files as $file) {
                wimport('helper.content.'.$file);
                $contentHelpers[] = ucfirst(substr(substr($file, -4), 0, 8));
            };
        }

        return self::$contentHelpers;
    }
}
