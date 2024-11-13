<template>
    <div>
        <h1>Product List</h1>

        <!-- Check if products are still loading -->
        <div v-if="loading">Loading...</div>

        <!-- If there are no products -->
        <div v-else-if="products.length === 0">
            No products available.
        </div>

        <!-- Display list of products -->
        <div v-else>
            <ul>
                <li v-for="product in products" :key="product.id">
                    <strong>{{ product.title }}</strong>
                    <p>Product ID: {{ product.id }}</p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            products: [],
            loading: true,
        };
    },
    mounted() {
        // Fetch products when the component is mounted
        this.fetchProducts();
    },
    methods: {
        async fetchProducts() {
            try {
                const response = await axios.get('/api/products');
                this.products = response.data.data;
                this.loading = false;
            } catch (error) {
                console.error('There was an error fetching the products:', error);
                this.loading = false;
            }
        }
    }
};
</script>

<style scoped>
/* You can add your custom styles here */
</style>
