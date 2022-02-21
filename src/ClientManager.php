<?php

namespace Drupal\es_connector;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Transport;

/**
 * Class ClientManager.
 *
 * Provides method to build an Elasticsearch Client using Cluster entity.
 */
class ClientManager extends ClientBuilder {

  /**
   * Create an Elasticsearch client from a Cluster entity.
   */
  public static function buildFromCluster(ClusterInterface $cluster): Client {
    if (!$cluster->isActive()) {
      // Don't attempt to build client if cluster has been disabled in UI.
      throw new \Exception('Cluster is not active.');
    }

    return static::create()
      ->setHosts([$cluster->getUrl()])
      ->setBasicAuthentication($cluster->getUsername(), $cluster->getPassword())
      ->build();
  }

  /**
   * {@inheritdoc}
   *
   * Called from the build method, overrides Elasticsearch\ClientBuilder method
   * returns our custom Client class so we have control over getting results.
   */
  protected function instantiate(Transport $transport, callable $endpoint, array $registeredNamespaces): Client {
    return new Client($transport, $endpoint, $registeredNamespaces);
  }

}
