<template>
    <div>
        <nav>
            <router-link to="/">Home</router-link>
            <router-link to="/products">Products</router-link>
        </nav>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: 'App',
};
</script>

<style>
/* Your styles for the App component */
nav {
    margin-bottom: 20px;
}
nav a {
    margin-right: 10px;
    text-decoration: none;
}
</style>
