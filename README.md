# shopify-laravel-demo-app

## Assignment

### Module 5

Go to branch [assignment-project-module-5](https://github.com/shantudas/shopify-laravel-demo-app/tree/assignment-project-module-5)

### Instructions
![Shop Screenshot](https://raw.githubusercontent.com/shantudas/shopify-laravel-demo-app/screenshots/public/images/sc_shop.png)
![Products Screenshot](https://raw.githubusercontent.com/shantudas/shopify-laravel-demo-app/screenshots/public/images/sc_prducts.png)


### Instructions
1. set .env for shopify
2. migrate the project
3. Register webhook for bulk operation finish
4. create bulk operation graphql query from postman to fetch product list
5. Go to [https://your_app_url/shop](https://your_app_domain/shop) to shop details
6. Go to [https://your_app_url/products](https://your_app_domain/products) to product list

.env 
```
SHOPIFY_DEBUG=true
SHOPIFY_MANUAL_MIGRATIONS=true
SHOPIFY_API_KEY=
SHOPIFY_API_SECRET=
```
Register webhook 
```
{"webhook":{"address":"https://your_app_domain/webhook/bulk-operations-finish","topic":"bulk_operations/finish","format":"json"}}
```

BUlk operation url
```
https://{{store-name}}.myshopify.com/admin/api/2024-10/graphql.json
```

bulk operation graphql query
```
mutation {
  bulkOperationRunQuery(
    query: """
    {
      products {
        edges {
          node {
            id
            title
            status
            images(first: 1) {
              edges {
                node {
                  src
                  altText
                }
              }
            }
            variants(first: 10) {
              edges {
                node {
                  id
                  title
                  price
                }
              }
            }
          }
        }
      }
    }
    """
  ) {
    bulkOperation {
      id
      status
    }
    userErrors {
      field
      message
    }
  }
}

```
