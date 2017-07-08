<?php

class CustomSearchIndex extends SolrIndex {

	function init() {
		// Add classes to index
		$this->addClass('SiteTree');

		// Add fields to index
		$this->addAllFulltextFields();

		// Allow opt-out from search results
		$this->addFilterField('ShowInSearch');

		// Don't index draft content
		$this->excludeVariantState(array('SearchVariantVersioned' => 'Stage'));

		// Boost field relevancy
		$this->addBoostedField('Title', null, array(), 1.5);
	}
}
