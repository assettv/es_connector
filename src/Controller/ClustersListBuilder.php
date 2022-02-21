<?php

namespace Drupal\es_connector\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\es_connector\ClientManager;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;

/**
 * Provides a listing of Clusters.
 */
class ClustersListBuilder extends ConfigEntityListBuilder {

  /**
   * Client Manager service.
   *
   * @var \Drupal\es_connector\ClientManager
   */
  protected ClientManager $clientManager;

  /**
   * Logging service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected LoggerChannelInterface $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    EntityStorageInterface $storage,
    ClientManager $client_manager,
    LoggerChannelFactoryInterface $log_factory
  ) {
    parent::__construct($entity_type, $storage);
    $this->clientManager = $client_manager;
    $this->logger = $log_factory->get('es_connector');
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(
    ContainerInterface $container,
    EntityTypeInterface $entity_type
  ) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('es_connector.client_manager'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Cluster');
    $header['id'] = $this->t('Machine name');
    $header['active'] = $this->t('Active');
    $header['version'] = $this->t('Version');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['active'] = $entity->get('active') ? $this->t('Yes') : $this->t('No');

    try {
      $client = $this->clientManager::buildFromCluster($entity);
      $health = $client->cluster()->health();
      $client_info = $client->info();
      $row['version'] = $client_info['version']['number'];
      $row['status'] = ucfirst($health['status']);
    }
    catch (\Exception $e) {
      $this->logger->error('Unable to get cluster health. %message', [
        '%message' => $e->getMessage(),
      ]);
      $row['version'] = 'Unavailable';
      $row['status'] = 'Unavailable';
    }

    return $row + parent::buildRow($entity);
  }

}
