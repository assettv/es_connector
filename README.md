# ES Connector
Middleman between Elasticsearch PHP library and Drupal. Elasticsearch Clusters
are modelled using config entities, which can be configured from
`/admin/config/search/es_clusters`.
## Installation
### Prepare Rep
Add Repo to your `composer.json` file
```
{
    "type": "vcs",
    "url": "git@github.com:assettv/es_connector.git"
}
```

### Add Vendor
Install View 2 JS in your composer.json: `composer require assettv/es_connector`

## Creating an Elasticsearch client

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

