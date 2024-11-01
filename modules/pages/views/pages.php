<?php
class pagesViewWcp extends viewWcp {
    public function displayDeactivatePage() {
        $this->assign('GET', reqWcp::get('get'));
        $this->assign('POST', reqWcp::get('post'));
        $this->assign('REQUEST_METHOD', strtoupper(reqWcp::getVar('REQUEST_METHOD', 'server')));
        $this->assign('REQUEST_URI', basename(reqWcp::getVar('REQUEST_URI', 'server')));
        parent::display('deactivatePage');
    }
}

