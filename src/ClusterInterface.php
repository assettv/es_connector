<?php

namespace Drupal\es_connector;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining an Elasticsearch Cluster entity.
 */
interface ClusterInterface extends ConfigEntityInterface {

  /**
   * Admin interface label for Cluster.
   */
  public function getLabel() : string;

  /**
   * Url for Elasticsearch server.
   */
  public function getUrl() : string;

  /**
   * Username for basic authentication on ES server.
   */
  public function getUsername() : string;

  /**
   * Password for basic authentication on ES server.
   */
  public function getPassword() : string;

  /**
   * Is a Cluster Active?
   *
   * Safety switch so connections to live Clusters can be
   * disabled on development/local environments.
   */
  public function isActive(): bool;

}
