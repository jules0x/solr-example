<?php

class Page extends SiteTree {

    private static $db = array();

    private static $has_one = array();

	public function getSearchTerm() {
		return Controller::curr()->getRequest()->getVar('searchterm');
	}

	public function getSearchCrumbs() {
		$root = $this->Level(1);

		if ($this->Level(3)) {
			$section = $this->Level(2);
			return $root->MenuTitle . '<span class="searchresults-separator" aria-hidden="true">›</span>' . $section->MenuTitle;
		} else {
			return $root->MenuTitle;
		}
	}
}
