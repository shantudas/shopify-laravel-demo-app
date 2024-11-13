<?php namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use stdClass;

class BulkOperationsFinishJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain|string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string $shopDomain The shop's myshopify domain.
     * @param stdClass $data The webhook data (JSON decoded).
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Convert domain
        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);

//        logger()->info('product list', (array)$this->data);

        if ($this->data->status === 'completed') {
//            $bulkOperationId = str_replace("gid://shopify/BulkOperation/", "", $this->data->admin_graphql_api_id);
//            logger()->info("bulk operation id:: $bulkOperationId");
            $this->fetchAndStoreProducts();
        }

        // Do what you wish with the data
        // Access domain name as $this->shopDomain->toNative()
    }

    /**
     * Fetch the JSONL file and store products in the database
     *
     * @param string $url The URL to download the JSONL file
     * @return void
     */
    protected function fetchAndStoreProducts()
    {
        // Your Shopify credentials
        $shop = $this->shopDomain->toNative();
        $accessToken = 'shpua_06ddf032512f4c6aeb03c5db360b721e';

        logger()->info("fetchAndStoreProducts :: $shop");
        logger()->info("fetchAndStoreProducts :: $accessToken");

        // GraphQL query
        $query = '{
          currentBulkOperation {
            id
            status
            errorCode
            createdAt
            completedAt
            objectCount
            fileSize
            url
          }
        }';

        // Make the request
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
        ])->post("https://$shop/admin/api/2024-10/graphql.json", [
            'query' => $query,
        ]);

        // Handle the response
        $data = $response->json();

        if (isset($data['data']['currentBulkOperation'])) {
            $bulkOperation = $data['data']['currentBulkOperation'];
            // You can now access the status, url, and other properties
            logger()->info('Bulk Operation Data', $bulkOperation);

            $url = $bulkOperation['url'];

            // Download the JSONL data
            $response = Http::get($url);

            if ($response->successful()) {
                $jsonlData = $response->body();
                logger()->info('Bulk Operation json string', (array)$jsonlData);
                // Now you have the JSONL data as a string
                $this->saveData($jsonlData);

            } else {
                logger()->error("Failed to download JSONL file.");
            }

        }
    }

    private function saveData(string $jsonlData)
    {
        $lines = explode("\n", $jsonlData);
        logger()->info("saveData lines ::",$lines);


//        foreach ($lines as $line) {
//            if (trim($line) !== '') {
//                $jsonlData = json_decode($line, true);
//            }
//        }
        $jsonString = '[' . preg_replace('/}\s*{/', '},{', $jsonlData) . ']';
        $jsonData=json_decode($jsonString, true);

//        $jsonData = [
//            [
//                "id" => "gid://shopify/Product/9778927632668",
//                "title" => "The Minimal Snowboard",
//                "status" => "ACTIVE"
//            ],
//            [
//                "id" => "gid://shopify/ProductVariant/50125384024348",
//                "title" => "Default Title",
//                "price" => "885.95",
//                "__parentId" => "gid://shopify/Product/9778927632668"
//            ],
//            [
//                "id" => "gid://shopify/Product/9778927698204",
//                "title" => "The Videographer Snowboard",
//                "status" => "ACTIVE"
//            ],
//            [
//                "src" => "https://cdn.shopify.com/s/files/1/0895/3865/8588/files/Main.jpg?v=1727707425",
//                "altText" => "The top and bottom view of a snowboard...",
//                "__parentId" => "gid://shopify/Product/9778927698204"
//            ],
//            [
//                "id" => "gid://shopify/ProductVariant/50125384089884",
//                "title" => "Default Title",
//                "price" => "885.95",
//                "__parentId" => "gid://shopify/Product/9778927698204"
//            ]
//        ];

//        logger()->info("saveData jsonData ::",$jsonData);

        // Array to store product data
        $products = [];
        logger()->info("saveData products:: ",$products);

        // Loop through the JSON data
        foreach ($jsonData as $data) {
            // Check if the data represents a product (has id, title, and status)
            if (isset($data['id'], $data['title'], $data['status'])) {
                // Initialize product entry
                $products[$data['id']] = [
                    'id' => $data['id'],
                    'title' => $data['title'],
                    'status' => $data['status'],
                    'price' => null,
                    'src' => null
                ];
            }
        }

        // Loop again to attach variants and images
        foreach ($jsonData as $data) {
            // Check for variants with __parentId
            if (isset($data['__parentId'], $data['price']) && isset($products[$data['__parentId']])) {
                // Attach price to the corresponding product
                $products[$data['__parentId']]['price'] = $data['price'];
            }

            // Check for images with __parentId
            if (isset($data['__parentId'], $data['src']) && isset($products[$data['__parentId']])) {
                // Attach src to the corresponding product
                $products[$data['__parentId']]['src'] = $data['src'];
            }
        }

        logger()->info("saveData products :: ",$products);

        // Now you can save the products to the database
        foreach ($products as $product) {
            Product::updateOrCreate(
                ['shopify_id' => $product['id']], // Unique identifier
                [
                    'title' => $product['title'],
                    'status' => $product['status'],
                    'price' => $product['price'],
                    'image_src' => $product['src']
                ]
            );
        }
    }
}
