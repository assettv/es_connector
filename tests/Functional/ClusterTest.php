<?php

namespace Drupal\Tests\es_connector\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group es_connector
 */
class ClusterTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['es_connector'];

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($this->user);
  }

  /**
   * Tests that the cluster config page loads with a 200 response.
   */
  public function testLoad() {
    $this->drupalGet(Url::fromRoute('entity.es_cluster.collection'));
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests that we can create a new cluster without error.
   */
  public function testCreateCluster() {
    $form_data = [
      'label' => 'Test cluster',
      'id' => 'test_cluster',
      'url' => 'http://es.example.com',
      'username' => 'username',
      'password' => 'password',
      'active' => 0,
    ];
    $this->drupalGet(Url::fromRoute('entity.es_cluster.add_form'));
    $this->submitForm($form_data, 'Save');

    $this->assertSession()->pageTextContains('Cluster "Test cluster" created');
  }

  /**
   * Tests that create entity fails when form is blank.
   */
  public function testCreateClusterCannotBeBlank() {
    $form_data = [
      'label' => '',
      'id' => '',
      'url' => '',
      'username' => '',
      'password' => '',
      'active' => 0,
    ];
    $this->drupalGet(Url::fromRoute('entity.es_cluster.add_form'));
    $this->submitForm($form_data, 'Save');

    $this->assertSession()->pageTextContains('Administrative cluster label field is required.');
    $this->assertSession()->pageTextContains('Machine-readable name field is required.');
    $this->assertSession()->pageTextContains('Server URL field is required.');
    $this->assertSession()->pageTextContains('Username field is required.');
    $this->assertSession()->pageTextContains('Password field is required.');
  }

}
