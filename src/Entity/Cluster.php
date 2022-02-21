<?php

namespace Drupal\es_connector\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\es_connector\ClusterInterface;

/**
 * Defines the Elasticsearch Cluster entity.
 *
 * @ConfigEntityType(
 *   id = "es_cluster",
 *   label = @Translation("Elasticsearch Cluster"),
 *   handlers = {
 *     "list_builder" = "Drupal\es_connector\Controller\ClustersListBuilder",
 *     "form" = {
 *       "add" = "Drupal\es_connector\Form\AddClusterForm",
 *       "edit" = "Drupal\es_connector\Form\AddClusterForm",
 *       "delete" = "Drupal\es_connector\Form\DeleteClusterForm",
 *     }
 *   },
 *   config_prefix = "es_cluster",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "url",
 *     "username",
 *     "password",
 *     "active"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/search/es_clusters/{es_cluster}/edit",
 *     "delete-form" = "/admin/config/search/es_clusters/{es_cluster}/delete",
 *   }
 * )
 */
class Cluster extends ConfigEntityBase implements ClusterInterface {

  /**
   * The Cluster ID.
   *
   * @var string
   */
  protected string $id = '';

  /**
   * The Cluster label.
   *
   * @var string
   */
  protected string $label = '';

  /**
   * The server URL.
   *
   * @var string
   */
  protected string $url = '';

  /**
   * Elasticsearch authentication username.
   *
   * @var string
   */
  protected string $username = '';

  /**
   * Elasticsearch authentication password.
   *
   * @var string
   */
  protected string $password = '';

  /**
   * Flag to disable use of this cluster.
   *
   * @var bool
   */
  protected bool $active = FALSE;

  /**
   * Get the admin interface label.
   */
  public function getLabel() : string {
    return $this->label;
  }

  /**
   * Get the Cluster URL.
   */
  public function getUrl() : string {
    return $this->url;
  }

  /**
   * Get the Basic Auth Username.
   */
  public function getUsername() : string {
    return $this->username;
  }

  /**
   * Get the Basic Auth Password.
   */
  public function getPassword() : string {
    return $this->password;
  }

  /**
   * Is the Cluster active (query-able)?
   */
  public function isActive() : bool {
    return $this->active;
  }

}
