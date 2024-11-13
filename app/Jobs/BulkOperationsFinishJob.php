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

        if ($this->data->status === 'completed') {
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
            // access the status, url, and other properties
            logger()->info('Bulk Operation Data', $bulkOperation);

            $url = $bulkOperation['url'];

            // Download the JSONL data
            $response = Http::get($url);

            if ($response->successful()) {
                $jsonlData = $response->body();
                logger()->info('Bulk Operation json string', (array)$jsonlData);
                // Now you have the JSONL data as a string
                $this->storeProductData($jsonlData);

            } else {
                logger()->error("Failed to download JSONL file.");
            }

        }
    }

    private function storeProductData(string $jsonlData)
    {
        $jsonString = '[' . preg_replace('/}\s*{/', '},{', $jsonlData) . ']';
        $jsonData=json_decode($jsonString, true);

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

        // save the products to the database
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
