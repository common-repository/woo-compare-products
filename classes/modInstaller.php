<?php
class modInstallerWcp {
    static private $_current = array();
    /**
     * Install new moduleWcp into plugin
     * @param string $module new moduleWcp data (@see classes/tables/modules.php)
     * @param string $path path to the main plugin file from what module is installed
     * @return bool true - if install success, else - false
     */
    static public function install($module, $path) {
        $exPlugDest = explode('plugins', $path);
        if(!empty($exPlugDest[1])) {
            $module['ex_plug_dir'] = str_replace(DS, '', $exPlugDest[1]);
        }
        $path = $path. DS. $module['code'];
        if(!empty($module) && !empty($path) && is_dir($path)) {
            if(self::isModule($path)) {
                $filesMoved = false;
                if(empty($module['ex_plug_dir']))
                    $filesMoved = self::moveFiles($module['code'], $path);
                else
                    $filesMoved = true;     //Those modules doesn't need to move their files
                if($filesMoved) {
                    if(frameWcp::_()->getTable('modules')->exists($module['code'], 'code')) {
                        frameWcp::_()->getTable('modules')->delete(array('code' => $module['code']));
                    }
					if($module['code'] != 'license')
						$module['active'] = 0;
                    frameWcp::_()->getTable('modules')->insert($module);
                    self::_runModuleInstall($module);
                    self::_installTables($module);
                    return true;
                } else {
                    errorsWcp::push(sprintf(__('Move files for %s failed'), $module['code']), errorsWcp::MOD_INSTALL);
                }
            } else
                errorsWcp::push(sprintf(__('%s is not plugin module'), $module['code']), errorsWcp::MOD_INSTALL);
        }
        return false;
    }
    static protected function _runModuleInstall($module, $action = 'install') {
        $moduleLocationDir = WCP_MODULES_DIR;
        if(!empty($module['ex_plug_dir']))
            $moduleLocationDir = utilsWcp::getPluginDir( $module['ex_plug_dir'] );
        if(is_dir($moduleLocationDir. $module['code'])) {
			if(!class_exists($module['code']. strFirstUp(WCP_CODE))) {
				importClassWcp($module['code'], $moduleLocationDir. $module['code']. DS. 'mod.php');
			}
            $moduleClass = toeGetClassNameWcp($module['code']);
            $moduleObj = new $moduleClass($module);
            if($moduleObj) {
                $moduleObj->$action();
            }
        }
    }
    /**
     * Check whether is or no module in given path
     * @param string $path path to the module
     * @return bool true if it is module, else - false
     */
    static public function isModule($path) {
        return true;
    }
    /**
     * Move files to plugin modules directory
     * @param string $code code for module
     * @param string $path path from what module will be moved
     * @return bool is success - true, else - false
     */
    static public function moveFiles($code, $path) {
        if(!is_dir(WCP_MODULES_DIR. $code)) {
            if(mkdir(WCP_MODULES_DIR. $code)) {
                utilsWcp::copyDirectories($path, WCP_MODULES_DIR. $code);
                return true;
            } else 
                errorsWcp::push(__('Cannot create module directory. Try to set permission to '. WCP_MODULES_DIR. ' directory 755 or 777', WCP_LANG_CODE), errorsWcp::MOD_INSTALL);
        } else
            return true;
        return false;
    }
    static private function _getPluginLocations() {
        $locations = array();
        $plug = reqWcp::getVar('plugin');
        if(empty($plug)) {
            $plug = reqWcp::getVar('checked');
            $plug = $plug[0];
        }
        $locations['plugPath'] = plugin_basename( trim( $plug ) );
        $locations['plugDir'] = dirname(WP_PLUGIN_DIR. DS. $locations['plugPath']);
		$locations['plugMainFile'] = WP_PLUGIN_DIR. DS. $locations['plugPath'];
        $locations['xmlPath'] = $locations['plugDir']. DS. 'install.xml';
        return $locations;
    }
    static private function _getModulesFromXml($xmlPath) {
        if($xml = utilsWcp::getXml($xmlPath)) {
            if(isset($xml->modules) && isset($xml->modules->mod)) {
                $modules = array();
                $xmlMods = $xml->modules->children();
                foreach($xmlMods->mod as $mod) {
                    $modules[] = $mod;
                }
                if(empty($modules))
                    errorsWcp::push(__('No modules were found in XML file', WCP_LANG_CODE), errorsWcp::MOD_INSTALL);
                else
                    return $modules;
            } else
                errorsWcp::push(__('Invalid XML file', WCP_LANG_CODE), errorsWcp::MOD_INSTALL);
        } else
            errorsWcp::push(__('No XML file were found', WCP_LANG_CODE), errorsWcp::MOD_INSTALL);
        return false;
    }
    /**
     * Check whether modules is installed or not, if not and must be activated - install it
     * @param array $codes array with modules data to store in database
     * @param string $path path to plugin file where modules is stored (__FILE__ for example)
     * @return bool true if check ok, else - false
     */
    static public function check($extPlugName = '') {
		if(WCP_TEST_MODE) {
			add_action('activated_plugin', array(frameWcp::_(), 'savePluginActivationErrors'));
		}
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsWcp::xmlNodeAttrsToArr($m);
                if(!empty($modDataArr)) {
					//If module Exists - just activate it, we can't check this using frameWcp::moduleExists because this will not work for multy-site WP
                    if(frameWcp::_()->getTable('modules')->exists($modDataArr['code'], 'code') /*frameWcp::_()->moduleExists($modDataArr['code'])*/) {
                        self::activate($modDataArr);
                    } else {                                           //  if not - install it
                        if(!self::install($modDataArr, $locations['plugDir'])) {
                            errorsWcp::push(sprintf(__('Install %s failed'), $modDataArr['code']), errorsWcp::MOD_INSTALL);
                        }
                    }
                }
            }
        } else
            errorsWcp::push(__('Error Activate module', WCP_LANG_CODE), errorsWcp::MOD_INSTALL);
        if(errorsWcp::haveErrors(errorsWcp::MOD_INSTALL)) {
            self::displayErrors();
            return false;
        }
		update_option(WCP_CODE. '_full_installed', 1);
        return true;
    }
    /**
	 * Public alias for _getCheckRegPlugs()
	 */
	/**
	 * We will run this each time plugin start to check modules activation messages
	 */
	static public function checkActivationMessages() {

	}
    /**
     * Deactivate module after deactivating external plugin
     */
    static public function deactivate() {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsWcp::xmlNodeAttrsToArr($m);
                if(frameWcp::_()->moduleActive($modDataArr['code'])) { //If module is active - then deacivate it
                    if(frameWcp::_()->getModule('options')->getModel('modules')->put(array(
                        'id' => frameWcp::_()->getModule($modDataArr['code'])->getID(),
                        'active' => 0,
                    ))->error) {
                        errorsWcp::push(__('Error Deactivation module', WCP_LANG_CODE), errorsWcp::MOD_INSTALL);
                    }
                }
            }
        }
        if(errorsWcp::haveErrors(errorsWcp::MOD_INSTALL)) {
            self::displayErrors(false);
            return false;
        }
        return true;
    }
    static public function activate($modDataArr) {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsWcp::xmlNodeAttrsToArr($m);
                if(!frameWcp::_()->moduleActive($modDataArr['code'])) { //If module is not active - then acivate it
                    if(frameWcp::_()->getModule('options')->getModel('modules')->put(array(
                        'code' => $modDataArr['code'],
                        'active' => 1,
                    ))->error) {
                        errorsWcp::push(__('Error Activating module', WCP_LANG_CODE), errorsWcp::MOD_INSTALL);
                    } else {
						$dbModData = frameWcp::_()->getModule('options')->getModel('modules')->get(array('code' => $modDataArr['code']));
						if(!empty($dbModData) && !empty($dbModData[0])) {
							$modDataArr['ex_plug_dir'] = $dbModData[0]['ex_plug_dir'];
						}
						self::_runModuleInstall($modDataArr, 'activate');
					}
                }
            }
        }
    } 
    /**
     * Display all errors for module installer, must be used ONLY if You realy need it
     */
    static public function displayErrors($exit = true) {
        $errors = errorsWcp::get(errorsWcp::MOD_INSTALL);
        foreach($errors as $e) {
            echo '<b style="color: red;">'. $e. '</b><br />';
        }
        if($exit) exit();
    }
    static public function uninstall() {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsWcp::xmlNodeAttrsToArr($m);
                self::_uninstallTables($modDataArr);
                frameWcp::_()->getModule('options')->getModel('modules')->delete(array('code' => $modDataArr['code']));
                utilsWcp::deleteDir(WCP_MODULES_DIR. $modDataArr['code']);
            }
        }
    }
    static protected  function _uninstallTables($module) {
        if(is_dir(WCP_MODULES_DIR. $module['code']. DS. 'tables')) {
            $tableFiles = utilsWcp::getFilesList(WCP_MODULES_DIR. $module['code']. DS. 'tables');
            if(!empty($tableNames)) {
                foreach($tableFiles as $file) {
                    $tableName = str_replace('.php', '', $file);
                    if(frameWcp::_()->getTable($tableName))
                        frameWcp::_()->getTable($tableName)->uninstall();
                }
            }
        }
    }
    static public function _installTables($module, $action = 'install') {
		$modDir = empty($module['ex_plug_dir']) ? 
            WCP_MODULES_DIR. $module['code']. DS :
            utilsWcp::getPluginDir($module['ex_plug_dir']). $module['code']. DS;
        if(is_dir($modDir. 'tables')) {
            $tableFiles = utilsWcp::getFilesList($modDir. 'tables');
            if(!empty($tableFiles)) {
                frameWcp::_()->extractTables($modDir. 'tables'. DS);
                foreach($tableFiles as $file) {
                    $tableName = str_replace('.php', '', $file);
                    if(frameWcp::_()->getTable($tableName))
                        frameWcp::_()->getTable($tableName)->$action();
                }
            }
        }
    }
}