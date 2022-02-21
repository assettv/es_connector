## ES Connector

Middleman between Elasticsearch PHP library and Drupal. Elasticsearch Clusters
are modelled using config entities, which can be configured from
`/admin/config/search/es_clusters`.

### Creating an Elasticsearch client

Example:
```
// Instantiate client manager
$clientManager = \Drupal::service('es_connector.client_manager');

// Load the Cluster entity
$entity = \Drupal::entityTypeManager()
  ->getStorage('es_cluster')
  ->load('dev'); // Cluster entity machine name

// Build the client
$client = $clientManager::buildFromCluster($entity);

// Example search
/** @var \Drupal\es_connector\SearchResponse */
$results = $client->search([
  'index' => 'viewing_logs',
  'body'  => [
    'query' => [
      'match' => [
        'region' => 'assettv'
      ]
    ],
    'aggs' => [
      'videos' => ['terms' => ['field' => 'object_id']]
    ],
    'size' => 1
  ]
]);
```

