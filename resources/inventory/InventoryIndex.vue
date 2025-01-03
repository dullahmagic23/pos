<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-warehouse"></i> Inventories</h3>
            <div>
              <button class="btn btn-success btn-sm me-2" @click="exportToExcel">
                <i class="fas fa-file-excel"></i> Export to Excel
              </button>
              <button class="btn btn-danger btn-sm me-2" @click="exportToPDF">
                <i class="fas fa-file-pdf"></i> Export to PDF
              </button>
              <button class="btn btn-secondary btn-sm" @click="printPage">
                <i class="fas fa-print"></i> Print
              </button>
            </div>
          </div>
          <div class="card-body">
            <table class="table table-striped">
              <thead class="table-dark">
              <tr>
                <th scope="col">#</th>
                <th scope="col">Supplier</th>
                <th scope="col">Date</th>
                <th scope="col">Products</th>
                <th scope="col">Actions</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(inventory, index) in inventories" :key="inventory.id">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ inventory.supplier.name }}</td>
                <td>{{ formatDate(inventory.date) }}</td>
                <td>
                  <ul>
                    <li v-for="(order, idx) in inventory.orders" :key="idx">
                      {{ order.product.name }}
                      <ul>
                        <li v-for="(unit, uIdx) in order.units" :key="uIdx">
                          {{ unit.name }}: {{ unit.quantity }}
                        </li>
                      </ul>
                    </li>
                  </ul>
                </td>
                <td>
                  <button @click="viewOrders(inventory)" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#orderDetailsModal">
                    <i class="fas fa-eye"></i> View Orders
                  </button>
                  <button @click="deleteInventory(inventory.id)" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt"></i> Delete
                  </button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <h6 class="text-secondary" v-if="selectedInventoryOrders.length > 0">
              Supplier: {{ getSupplierName(selectedInventoryOrders) }}
            </h6>
            <table class="table table-striped">
              <thead class="table-light">
              <tr>
                <th scope="col">#</th>
                <th scope="col">Product</th>
                <th scope="col">Units</th>
                <th scope="col">Buying Price</th>
                <th scope="col">Selling Price</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(order, index) in selectedInventoryOrders" :key="order.id">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ order.product.name }}</td>
                <td>
                  <ul>
                    <li v-for="(unit, uIdx) in order.units" :key="uIdx">
                      {{ unit.name }}: {{ unit.quantity }}
                    </li>
                  </ul>
                </td>
                <td>{{ formatCurrency(order.buying_price) }}</td>
                <td>{{ formatCurrency(order.selling_price) }}</td>
              </tr>
              </tbody>
            </table>
            <button class="btn btn-primary" @click="printOrderDetails">
              <i class="fas fa-print"></i> Print Order
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import * as XLSX from 'xlsx';
import { saveAs } from 'file-saver';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

export default {
  data() {
    return {
      inventories: [],
      selectedInventoryOrders: [],
    };
  },
  mounted() {
    this.fetchInventories();
  },
  methods: {
    async fetchInventories() {
      try {
        const response = await axios.get('/api/inventory');
        this.inventories = response.data.map(inventory => ({
          ...inventory,
          orders: inventory.orders.map(order => ({
            ...order,
            units: order.units || [], // Ensure units are available
          })),
        }));
      } catch (error) {
        console.error('Error fetching inventories:', error);
      }
    },
    async viewOrders(inventory) {
      try {
        const response = await axios.get(`/api/inventory/${inventory.id}`);
        this.selectedInventoryOrders = response.data.orders.map(order => ({
          ...order,
          units: order.units || [], // Ensure units are available
        }));
      } catch (error) {
        console.error('Error fetching orders:', error);
      }
    },
    async deleteInventory(inventoryId) {
      if (confirm('Are you sure you want to delete this inventory?')) {
        try {
          await axios.delete(`/api/inventories/${inventoryId}`);
          this.inventories = this.inventories.filter(
              inventory => inventory.id !== inventoryId
          );
        } catch (error) {
          console.error('Error deleting inventory:', error);
        }
      }
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('en-US', options);
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      }).format(value);
    },
    getSupplierName(orders) {
      if (orders.length > 0) {
        return orders[0].supplier.name;
      }
      return '';
    },
    exportToExcel() {
      const wsData = this.inventories.flatMap((inventory, index) => {
        return inventory.orders.flatMap(order =>
            order.units.map(unit => ({
              '#': index + 1,
              Supplier: inventory.supplier.name,
              Date: this.formatDate(inventory.date),
              Product: order.product.name,
              Unit: unit.name,
              Quantity: unit.quantity,
            }))
        );
      });
      const ws = XLSX.utils.json_to_sheet(wsData);
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, 'Inventories');
      const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
      saveAs(new Blob([wbout], { type: 'application/octet-stream' }), 'inventories.xlsx');
    },
    exportToPDF() {
      const doc = new jsPDF('landscape');
      const columns = ['#', 'Supplier', 'Date', 'Product', 'Unit', 'Quantity'];
      const rows = this.inventories.flatMap((inventory, index) => {
        return inventory.orders.flatMap(order =>
            order.units.map(unit => [
              index + 1,
              inventory.supplier.name,
              this.formatDate(inventory.date),
              order.product.name,
              unit.name,
              unit.quantity,
            ])
        );
      });
      doc.autoTable(columns, rows);
      doc.save('inventories.pdf');
    },
    printPage() {
      window.print();
    },
    printOrderDetails() {
      const printContent = document.getElementById('orderDetailsModal').innerHTML;
      const originalContent = document.body.innerHTML;
      document.body.innerHTML = printContent;
      window.print();
      document.body.innerHTML = originalContent;
    },
  },
};
</script>

<style scoped>
.container {
  width: 100%;
}

.card-header {
  background-color: #007bff;
  border-bottom: 1px solid #e3e3e3;
  color: white;
}

.table-light {
  background-color: #f8f9fa;
}

.text-secondary {
  color: #6c757d;
}
</style>
