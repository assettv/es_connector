<?php

namespace Drupal\es_connector;

/**
 * Class SearchResponse.
 *
 * Wraps the array data returned from Elasticsearch, providing cleaner
 * API to extract results.
 */
class SearchResponse {

  /**
   * The response from execution of \Elasticsearch\Client::search() method.
   *
   * @var array
   */
  protected $response = [];

  /**
   * SearchResponse Constructor.
   *
   * @param mixed[] $response
   *   Response data from Elasticsearch Query.
   */
  public function __construct(array $response) {
    $this->response = $response;
  }

  /**
   * Get the time Elasticsearch spending on executing the search.
   *
   * @return int
   *   Time in milliseconds for Elasticsearch to execute the search.
   */
  public function getSearchTimeTook() {
    return $this->response['took'];
  }

  /**
   * Does Elasticsearch timed out on search execution.
   *
   * @return bool
   *   TRUE - timed out.
   *   FALSE - does not timed out.
   */
  public function hasTimeout() {
    return (bool) $this->response['timed_out'];
  }

  /**
   * Get the total number of shards searched by Elasticsearch.
   *
   * @return int
   *   Number of shards.
   */
  public function getTotalShards() {
    return $this->response['_shards']['total'];
  }

  /**
   * Get the number of failed shards searched by Elasticsearch.
   *
   * @return int
   *   Number of failed shards.
   */
  public function getFailedShards() {
    return $this->response['_shards']['failed'];
  }

  /**
   * Get the number of successful shards searched by Elasticsearch.
   *
   * @return int
   *   Number of successful shards.
   */
  public function getSuccessfulShards() {
    return $this->response['_shards']['successful'];
  }

  /**
   * Get the max score of the search.
   *
   * @return float
   *   The max score of the search calculate by Elasticsearch.
   */
  public function getSearchMaxScore() {
    return $this->response['hits']['max_score'];
  }

  /**
   * Get the total number of results.
   *
   * @return int
   *   The total number of results.
   */
  public function getTotalResultsNumber() {
    return $this->response['hits']['total']['value'];
  }

  /**
   * Get the raw response from Elasticsearch.
   *
   * @return array
   *   Data straight as it came from Elasticsearch.
   */
  public function getRawResponse() {
    return $this->response;
  }

  /**
   * Get the raw results from Elasticsearch.
   *
   * @return array
   *   Hits straight from Elasticsearch results.
   */
  public function getRawResults() {
    if (isset($this->response['hits']['hits'])) {
      return $this->response['hits']['hits'];
    }

    return [];
  }

  /**
   * Get search results keyed by ID.
   *
   * @return array
   *   Results keyed by ID.
   */
  public function getResults() {
    $return = [];
    foreach ($this->getRawResults() as $result) {
      $return[$result['_id']] = $result['_source'];
    }
    return $return;
  }

  /**
   * Get raw aggregation results.
   *
   * @return array
   *   Aggregation results.
   */
  public function getRawAggregations() {
    if (isset($this->response['aggregations'])) {
      return $this->response['aggregations'];
    }

    return [];
  }

}
