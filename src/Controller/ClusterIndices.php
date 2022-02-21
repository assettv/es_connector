<?php

namespace Drupal\es_connector\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\es_connector\ClientManager;
use Drupal\es_connector\ClusterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;

/**
 * Controller Class ClusterIndices.
 */
class ClusterIndices extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Client Manager service.
   *
   * @var \Drupal\es_connector\ClientManager
   */
  protected ClientManager $clientManager;

  /**
   * Logging channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected LoggerChannelInterface $logger;

  /**
   * ClusterIndices Constructor.
   */
  public function __construct(ClientManager $client_manager, LoggerChannelFactoryInterface $log_factory) {
    $this->clientManager = $client_manager;
    $this->logger = $log_factory->get('es_connector');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('es_connector.client_manager'),
      $container->get('logger.factory')
    );
  }

  /**
   * Generate the table of indices on the cluster.
   */
  public function list(ClusterInterface $es_cluster) {
    try {
      $client = $this->clientManager->buildFromCluster($es_cluster);
      $indices = $client->indices()->stats();
    }
    catch (\Exception $e) {
      $this->logger->error('Unable to load indices: ' . $e->getMessage());

      return [
        '#markup' => $this->t('Unable to get indices from Elasticsearch.'),
      ];
    }

    $rows = [];

    if (!empty($indices['indices'])) {
      foreach ($indices['indices'] as $index_name => $index_info) {
        $rows[] = [
          ['data' => $index_name],
          ['data' => $index_info['total']['docs']['count']],
          ['data' => format_size($index_info['total']['store']['size_in_bytes'])],
        ];
      }
    }

    return [
      '#theme' => 'table',
      '#header' => [
        ['data' => $this->t('Name')],
        ['data' => $this->t('Records')],
        ['data' => $this->t('Size')],
      ],
      '#rows' => $rows,
    ];
  }

  /**
   * Admin page title callback.
   */
  public function pageTitle(ClusterInterface $es_cluster) {
    return 'Search indices on ' . $es_cluster->getLabel() . ' cluster';
  }

}
