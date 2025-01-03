<template>
    <div class="container">
      <h1 class="my-4">
        <i class="fa fa-refresh"></i>
        Stock Conversion Tool</h1>
      <div class="mb-3">
        <label for="product" class="form-label">Product</label>
        <select class="form-select" id="product" v-model="product" @change="fetchUnits">
          <option :value="''" disabled>Select a Product</option>
          <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="unitFrom" class="form-label">From Unit (higher)</label>
        <select class="form-select" id="unitFrom" v-model="unitFrom">
          <option :value="''" disabled>Select the High Unit</option>
          <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="unitTo" class="form-label">To Unit (lower)</label>
        <select class="form-select" id="unitTo" v-model="unitTo">
          <option :value="''" disabled>Select the Lower Unit</option>
          <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="conversionRate" class="form-label">Conversion Rate</label>
        <select class="form-select" id="conversionRate" v-model="conversionRate">
          <option :value="''" disabled>Select Conversion Rate</option>
          <option v-for="rate in conversionRatesOptions" :key="rate" :value="rate">{{ rate }}</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="stockAmount" class="form-label">Stock Amount</label>
        <input type="number" class="form-control" id="stockAmount" v-model="stockAmount" />
      </div>
      <button class="btn btn-primary" @click="convertStock">
        <i :class="!loader?`fa fa-refresh`:`fa fa-spinner fa-spin`"></i>
        Convert
      </button>
      <div class="mt-4" v-if="convertedAmount !== null">
        <h3>Converted Amount: {{ convertedAmount }}</h3>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        stockAmount: 0,
        product: '',
        unitFrom: '',
        unitTo: '',
        conversionRate: '',
        products: [],
        units: [],
        conversionRates: {},
        conversionRatesOptions: [1, 6, 12, 24, 36, 25, 50, 100],
        convertedAmount: null,
        loader:false
      };
    },
    created() {
      this.fetchProducts();
    },
    methods: {
      async fetchProducts() {
        try {
          const response = await axios.get('/api/products');
          this.products = response.data;
        } catch (error) {
          console.error('Error fetching products:', error);
        }
      },
      async fetchUnits() {
        try {
          if (this.product) {
            const response = await axios.get(`/api/products/${this.product}/units`);
            this.units = response.data;
            await this.fetchConversionRates();
          }
        } catch (error) {
          console.error('Error fetching units:', error);
        }
      },
      async fetchConversionRates() {
        try {
          if (this.product) {
            const response = await axios.get(`/api/products/${this.product}/units`);
            this.conversionRates = response.data;
          }
        } catch (error) {
          console.error('Error fetching conversion rates:', error);
        }
      },
      convertStock() {
        if (this.unitFrom && this.unitTo && this.stockAmount && this.conversionRate) {
          try{
            this.loader = true;
            axios.post('/api/stocks/convert', {
              product_id: this.product,
              unitFrom: this.unitFrom,
              unitTo: this.unitTo,
              stockAmount: this.stockAmount,
              conversionRate: this.conversionRate,
            }).then((response) => {
              this.loader = false;
              alert(response.data.message);
            }, function (error) {
                alert('Error converting stock: '+error.response.data.message);
            });

          } catch (error) {
           alert(error.response.data.message);
          }
        } else {
          console.error('Please select valid units, enter a stock amount, and select a conversion rate.');
        }
      },
    },
  };
  </script>
  
  <style>
  /* Add any custom styles here */
  </style>
  