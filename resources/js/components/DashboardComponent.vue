<template>
    <div class="dashboard">
        <h2>Dashboard</h2>
        <div class="stats">
            <div class="stat">
                <h3>Total Users</h3>
                <p>{{ totalUsers }}</p>
            </div>
            <div class="stat">
                <h3>Total Payments</h3>
                <p>{{ totalPayments }}</p>
            </div>
            <div class="stat">
                <h3>Recent Activity</h3>
                <p>{{ recentActivity.length }}</p>
            </div>
        </div>

        <h4>Latest Payments</h4>
        <table class="table table-striped">
            <thead class="table-dark">
            <tr>
                <th>Payment ID</th>
                <th>Customer Name</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="payment in latestPayments" :key="payment.id">
                <td>{{ formatPaymentId(payment.id) }}</td>
                <td>{{ payment.customer.name }}</td>
                <td>{{ formatCurrency(payment.amount) }}</td>
                <td>{{ payment.payment_method.name }}</td>
                <td>{{ formatDate(payment.created_at) }}</td>
            </tr>
            <tr v-if="latestPayments.length === 0">
                <td colspan="5" class="text-center">No payments found.</td>
            </tr>
            </tbody>
        </table>

        <h4>Recent Activity</h4>
        <ul>
            <li v-for="activity in recentActivity" :key="activity.id">{{ activity.message }}</li>
        </ul>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            totalUsers: 0,
            totalPayments: 0,
            recentActivity: [],
            payments: [],
        };
    },
    computed: {
        latestPayments() {
            return this.payments.slice(0, 3);
        },
    },
    created() {
        this.fetchDashboardData();
    },
    methods: {
        async fetchDashboardData() {
            try {
                const [usersResponse, paymentsResponse, activityResponse] = await Promise.all([
                    axios.get('/api/users/total'),
                    axios.get('/api/payments'),
                    axios.get('/api/activity'),
                ]);

                this.totalUsers = usersResponse.data.total;
                this.payments = paymentsResponse.data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                this.totalPayments = this.payments.length;
                this.recentActivity = activityResponse.data;
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
            }
        },
        formatPaymentId(id) {
            return id.toString().padStart(6, '0');
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(value);
        },
        formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        },
    },
};
</script>

<style scoped>
.dashboard {
    padding: 20px;
}
.stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
}
.stat {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}
.table-dark {
    background-color: #343a40;
    color: white;
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.05);
}
.text-center {
    text-align: center;
}
</style>
