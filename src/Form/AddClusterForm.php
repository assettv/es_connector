<?php

namespace Drupal\es_connector\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to add/edit a Cluster Entity.
 */
class AddClusterForm extends EntityForm {

  /**
   * AddClusterForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entitytype Manager service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\es_connector\Entity\Cluster */
    $cluster = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Administrative cluster label'),
      '#default_value' => empty($cluster->getLabel()) ? '' : $cluster->getLabel(),
      '#description' => $this->t(
        'Enter an administrative label to identify your Elasticsearch cluster.'
      ),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $cluster->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$cluster->isNew(),
    ];

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Server URL'),
      '#default_value' => !empty($cluster->getUrl()) ? $cluster->getUrl() : '',
      '#description' => $this->t(
        'URL and port of a server (node) in the cluster.
        Please, always enter the port even if it is default one.
        Nodes will be automatically discovered.
        Examples: http://localhost:9200 or https://localhost:443.'
      ),
      '#required' => TRUE,
    ];

    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#description' => $this->t('Basic auth username.'),
      '#default_value' => (!empty($cluster->getUsername()) ? $cluster->getUsername() : ''),
      '#required' => TRUE,
    ];

    $form['password'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Password'),
      '#description' => $this->t('Basic auth password.'),
      '#default_value' => (!empty($cluster->getPassword()) ? $cluster->getPassword() : ''),
      '#required' => TRUE,
    ];

    $form['active'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Active'),
      '#default_value' => (!empty($cluster->isActive()) ? 1 : 0),
    ];

    return $form;
  }

  /**
   * Perform some brief validation.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!filter_var($form_state->getValue('url'), FILTER_VALIDATE_URL)) {
      $form_state->setErrorByName('url', $this->t('Please provide a valid URL.'));
    }
  }

  /**
   * Helper function to check whether a cluster exists.
   */
  public function exist($id) {
    $entity = $this->entityTypeManager->getStorage('es_cluster')->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = $this->entity->save();

    $this->messenger()->addMessage($this->t('Cluster "%label" %action', [
      '%label' => $this->entity->label(),
      '%action' => $status === SAVED_NEW ? 'created' : 'updated',
    ]));

    $form_state->setRedirect('entity.es_cluster.collection');
  }

}
