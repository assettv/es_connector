<?php

namespace Drupal\Tests\es_connector\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\es_connector\ClientManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\es_connector\Entity\Cluster;

/**
 * Test that a Cluster marked as inactive does not try to access data.
 *
 * @group es_connector
 */
class ClientManagerTest extends UnitTestCase {

  /**
   * Real Client Manager service.
   *
   * @var \Drupal\es_connector\ClientManager
   */
  protected $clientManager;

  /**
   * Mocked Cluster Entity Storage service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $clusterStorage;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $entity_storage = $this->createMock(EntityStorageInterface::class);
    $es_cluster = $this->createMock(Cluster::class);

    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject */
    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_type_manager->expects($this->any())
      ->method('getStorage')
      ->with('es_cluster')
      ->willReturn($entity_storage);

    /** @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit\Framework\MockObject\MockObject */
    $this->clusterStorage = $entity_type_manager->getStorage('es_cluster');
    $this->clusterStorage->expects($this->any())
      ->method('create')
      ->willReturn($es_cluster);

    $this->clientManager = new ClientManager();
  }

  /**
   * A Cluster marked as inactive must throw an exception when trying to build.
   */
  public function testInactiveClusterAreRejected() {
    $cluster = $this->clusterStorage->create([
      'label' => 'Test cluster',
      'id' => 'test_cluster',
      'url' => 'http://es.example.com',
      'username' => 'username',
      'password' => 'password',
      'active' => 0,
    ]);

    $this->expectException('\Exception');
    $this->expectExceptionMessage('Cluster is not active.');

    $this->clientManager::buildFromCluster($cluster);
  }

}
