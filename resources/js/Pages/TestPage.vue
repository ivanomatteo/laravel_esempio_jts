<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3'

defineProps({ user: Object, products: Object })


</script>

<template>
  <Head title="Welcome" />
  <h1>Welcome</h1>
  <p>Hello {{ user }}, welcome to your first Inertia app!</p>


  <pre>{{ products }}</pre>

  <button @click="router.visit('', { data: { page: 3 } });">next</button>
  <button @click="router.reload({ only: ['products'] })">reload</button>


  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
</template>