<?php

/**
 * Handles searches based on search query terms
 * The rules for the controller are defined in routes.yml
 * */
class Search_Controller extends Page_Controller {

	private static $allowed_actions = array(
		'index'
	);

	public static $search_index_class = 'CustomSearchIndex';

	public static $results_per_page = 10;

	private $results = null;

	public function Link($action = null) {
		return "search";
	}

	// Render a search results page
	public function index($request) {
		return $this->doSearch($request);
	}

	// Search
	private function doSearch($request) {
		$results = new ArrayList();
		$limit = self::$results_per_page;
		$suggestion = null;
		$showSuggestion = false;
		$totalMatches = 0;

		// Search parameters
		$start = (($request->getVar('start') && is_numeric($request->getVar('start'))) ? $request->getVar('start') : 0);
		$keyword = $request->getVar('searchterm');

		// No search term
		if (is_null($keyword) || strlen($keyword) <= 0) {
			return $this->renderWith(array('Page_results', 'Page'), array(
				'Results' => 0,
				'NoSearchTerm' => 1
			));
		} else {

			// Strip out question marks, as they're a Solr single character wildcard (and cannot be easily removed via Solr)
			$searchKeyword = str_replace("?", "", $keyword);

			// Exclude draft content
			Versioned::reading_stage('Live');

			// Get the known searchable page types from config
			$searchablePageTypes = Config::inst()->get('Search_Controller', 'indexed_pagetypes');

			// Build Query
			$query = new SearchQuery();
			$query->search($searchKeyword);

			// Exclude pages
			$query->exclude('SiteTree_ShowInSearch', 0);

			// Only allow configured page types
			$query->filter('ClassName', $searchablePageTypes);

			// Search
			try {
				$result = singleton('CustomSearchIndex')->search(
					$query,
					$start,
					$limit,
					array(
						'hl' => 'true',
						'spellcheck' => 'true',
						'spellcheck.collate' => 'true'
					)
				);

				// Spell correction
				$suggestion = $result->Suggestion;
				$suggestion = str_replace("+", "", $suggestion);

				// If there are no results, and a spelling suggestion is present, search again with that suggestion
				if ($result->Matches->totalItems == 0 && !empty($suggestion)) {

					$showSuggestion = true;
					$query = new SearchQuery();
					$query->search($suggestion);
					$query->exclude('SiteTree_ShowInSearch', 'false');

					$result = singleton('CustomSearchIndex')->search(
						$query,
						0,
						$limit,
						array(
							'hl' => 'true'
						)
					);
				}

				// Populate parameters
				$results = $result->Matches;
				$totalMatches = $result->Matches->totalItems;

			} catch (Exception $e) {
				SS_Log::log($e, SS_Log::WARN);
			}
		}

		// Populate parameters
		$this->results = $results;
		$pages = new PaginatedList($this->results, Controller::curr()->request);

		if (Director::isDev()) {
			$pages->setPageLength(2);
		} else {
			$pages->setPageLength(5);
		}

		// Return
		if ($request->isAjax()) {
			return $this->customise(new ArrayData(array(
				'TotalPages' => $totalMatches,
				'Results' => $results
			)))->renderWith('SearchResults');

		} else {
			return $this->renderWith(array('Page_results', 'Page'), array(
				'Results' => $results,
				'Query' => $keyword,
				'PaginatedResults' => $pages,
				'ShowSuggestion' => $showSuggestion,
				'Suggestion' => $suggestion,
				'TotalResults' => $totalMatches
			));
		}
	}

	// Search result page should have a unique page title per query
	public function Title() {
		$title = 'Search';

		if (!$this->request instanceof SS_HTTPRequest) {
			return $title;
		}

		$keyword = $this->request->getVar('searchterm');

		if ($keyword == '') {
			return $title;
		}

		if (is_null($this->results) || count($this->results) == 0) {
			$title = _t(
				'Search.NORESULTSTITLE',
				'No results found for {keyword}',
				'Page title when no results found',
				array('keyword' => $keyword)
			);
		}
		else {
			$title = 'Search results for "' . $keyword . '"';
		}

		return $title;
	}
}
