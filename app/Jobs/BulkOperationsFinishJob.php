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

        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $data = json_decode($line, true);

                if (isset($data['__parentId'])) {
                    // Save variant
                    Product::updateOrCreate(
                        ['shopify_id' => $data['id']], // Unique identifier
                        [
                            'title' => $data['title'],
                            'price' => $data['price'],
                            'parent_id' => $data['__parentId'] // Assuming you have a parent_id column
                        ]
                    );
                } else {
                    // Save product
                    Product::updateOrCreate(
                        ['shopify_id' => $data['id']], // Unique identifier
                        [
                            'title' => $data['title']
                        ]
                    );
                }
            }
        }
    }
}
