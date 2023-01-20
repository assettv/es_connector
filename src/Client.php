<?php

namespace Drupal\es_connector;

use Elasticsearch\Client as ElasticsearchClient;

/**
 * Class Client.
 *
 * Builds on Client from Elasticsearch PHP library.
 */
class Client extends ElasticsearchClient {

  /**
   * {@inheritdoc}
   *
   * Overrides the original search method to return our custom response class.
   */
  public function search(array $params = []): SearchResponse {
    $results = parent::search($params);
    return new SearchResponse($results);
  }

  /**
   * {@inheritdoc}
   */
  public function isClusterOk() {
    $health = $this->cluster()->health();
    return in_array($health['status'], ['green', 'yellow']);
  }

}
