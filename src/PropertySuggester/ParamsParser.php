<?php

namespace PropertySuggester;

use InvalidArgumentException;

/**
 * Parses the suggester parameters
 *
 * @licence GNU GPL v2+
 */
class ParamsParser {

	/**
	 * @var int
	 */
	private $defaultSuggestionSearchLimit;

	/**
	 * @var float
	 */
	private $defaultMinProbability;

	/**
	 * @param int $defaultSuggestionSearchLimit
	 * @param float $defaultMinProbability
	 */
	public function __construct( $defaultSuggestionSearchLimit, $defaultMinProbability ) {
		$this->defaultSuggestionSearchLimit = $defaultSuggestionSearchLimit;
		$this->defaultMinProbability = $defaultMinProbability;
	}

	/**
	 * parses and validates the parameters of GetSuggestion
	 * @param array $params
	 * @throws InvalidArgumentException
	 * @return Params
	 */
	public function parseAndValidate( array $params ) {
		$result = new Params();

		$result->entity = $params['entity'];
		$result->properties = $params['properties'];

		if ( !( $result->entity XOR $result->properties ) ) {
			throw new InvalidArgumentException( 'provide either entity-id parameter \'entity\' or a list of properties \'properties\'' );
		}

		// The entityselector doesn't allow a search for '' so '*' gets mapped to ''
		if ( $params['search'] !== '*' ) {
			$result->search = trim( $params['search'] );
		} else {
			$result->search = '';
		}

		$result->limit = $params['limit'];
		$result->continue = $params['continue'];
		$result->$internalResultListSize = $result->limit + $result->continue;
		$result->language = $params['language'];

		if ( $result->search ) {
			// the results matching '$search' can be at the bottom of the list
			// however very low ranked properties are not interesting and can
			// still be found during the merge with search result later.
			$result->suggesterLimit = $this->defaultSuggestionSearchLimit;
			$result->minProbability = 0.0;
		} else {
			$result->suggesterLimit = $result->$internalResultListSize;
			$result->minProbability = $this->defaultMinProbability;
		}

		return $result;
	}

}