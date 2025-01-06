/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';


/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});
import Notifications from '@kyvg/vue3-notification'
import ExampleComponent from './components/ExampleComponent.vue';
import UserIndex from "./components/users/UserIndex.vue";
import UserCreate from "./components/users/UserCreate.vue";
import UserEdit from "./components/users/UserEdit.vue";
import RolesIndex from "./components/roles/RolesIndex.vue";
import CustomerCreate from "./components/customers/CustomerCreate.vue";
import ManageCustomers from "./components/customers/ManageCustomers.vue";
import CreateSupplier from "./components/supplier/CreateSupplier.vue";
import SupplierIndex from "./components/supplier/SupplierIndex.vue";
import CategoriesIndex from "./components/categories/CategoriesIndex.vue";
import IndexUnit from "./components/units/IndexUnit.vue";
import Login from "./components/auth/Login.vue";
import IndexProduct from "./components/products/IndexProduct.vue";
import ProductCreate from "./components/products/ProductCreate.vue";
import StockCreate from "./components/stocks/StockCreate.vue";
import StockIndex from "./components/stocks/StockIndex.vue";
import InventoryCreate from "../inventory/InventoryCreate.vue";
import InventoryIndex from "../inventory/InventoryIndex.vue";
import InventoryAll from "../inventory/InventoryAll.vue";
import PosIndex from "./components/pos/PosIndex.vue";
import SalesIndex from "./components/SalesIndex.vue";
import PaymentsIndex from "./components/payments/PaymentsIndex.vue";
import PaymentsCreate from "./components/payments/PaymentsCreate.vue";
import ReportIndex from "./components/products/ReportIndex.vue";
import OutOfStock from "./components/OutOfStock.vue";
import BestSelling from "./components/BestSelling.vue";
import LatestTransaction from "./components/LatestTransaction.vue";
import ExpiredProducts from "./components/ExpiredProducts.vue";
import CreateInvoice from "./components/invoices/CreateInvoice.vue";
import InvoiceIndex from "./components/invoices/InvoiceIndex.vue";
import InvoiceComponent from "./components/invoices/InvoiceComponent.vue";
import ExpenseIndex from "./components/expenses/ExpenseIndex.vue";
import ExpenseCreate from "./components/expenses/ExpenseCreate.vue";
import ExpenseShow from "./components/expenses/ExpenseShow.vue";
import ConvertCompnent from './components/stocks/ConvertCompnent.vue';
import DiscountIndex from './components/discounts/DiscountIndex.vue';
import CreateDiscount from './components/discounts/CreateDiscount.vue';
import ChangePrice from "./components/products/ChangePrice.vue";
import CreateCompany from "./components/companies/CreateCompany.vue";
import ProductKey from "./components/ProductKey.vue";
app.component('example-component', ExampleComponent);
app.component('user-index',UserIndex)
app.component('create-user',UserCreate)
app.component('user-edit',UserEdit)
app.component('role-index',RolesIndex)
app.component('customer-create',CustomerCreate)
app.component('customer-index', ManageCustomers)
app.component('create-supplier',CreateSupplier)
app.component('index-supplier',SupplierIndex)
app.component('index-category',CategoriesIndex)
app.component('index-unit',IndexUnit)
app.component('login',Login)
app.component('index-product',IndexProduct)
app.component('create-product',ProductCreate)
app.component('stock-create', StockCreate)
app.component('stock-index',StockIndex)
app.component('inventory-create', InventoryCreate)
app.component('inventory-index',InventoryIndex)
app.component('all-inventory',InventoryAll)
app.component('pos-index',PosIndex)
app.component('sales-index',SalesIndex)
app.component('payments-create',PaymentsCreate)
app.component('payments-index',PaymentsIndex)
app.component('sales-report', ReportIndex)
app.component('out-stock',OutOfStock)
app.component('best-selling', BestSelling)
app.component('latest-transactions',LatestTransaction)
app.component('expired-products',ExpiredProducts)
app.component('create-invoice', CreateInvoice)
app.component('invoice-index', InvoiceIndex)
app.component('invoice-component', InvoiceComponent)
app.component('expense-index', ExpenseIndex)
app.component('expense-create', ExpenseCreate)
app.component('expense-show', ExpenseShow)
app.component('convert-component',ConvertCompnent)
app.component('discount-index',DiscountIndex)
app.component('discount-create',CreateDiscount)
app.component('change-price', ChangePrice)
app.component('create-company', CreateCompany)
app.component('product-key', ProductKey)
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg /components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to an HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */
app.use(Notifications)
app.mount('#app');
