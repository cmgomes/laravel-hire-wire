<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Seu Painel ACC',
        href: '/dashboard',
    },
];

const accounts = ref([]);
const transactions = ref([]);
const page = usePage();
const appUrl = computed(() => page.props.appUrl);

onMounted(async () => {
    try {
        const response = await fetch('/api/user/accounts');
        if (!response.ok) {
            const message = `Erro ao buscar contas: ${response.status}`;
            throw new Error(message);
        }
        const data = await response.json();
        accounts.value = data;
        console.log(accounts);
    } catch (error) {
        console.error("Erro ao buscar contas:", error);
    }

    try {
        const responseTransaction = await fetch('/api/transactions/last-week');
        if (!responseTransaction.ok) {
            const message = `Erro ao buscar transações: ${responseTransaction.status}`;
            throw new Error(message);
        }
        const data = await responseTransaction.json();
        transactions.value = data;
    } catch (error) {
        console.error("Erro ao buscar transações:", error);
    }
});

const formatCurrency = (valueInCents: number): string => {
    const valueInReais = valueInCents / 100;
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valueInReais);
};

</script>

<template>
    <Head title="Seu Painel ACC" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 ">
            <div class="relative grid md:grid-cols-2 gap-4 flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min ">
                <div class="min-h-[100vh] md:min-h-min">
                    <img src="http://localhost:9000/images/logo-accbank.jpg" class="max-h-[470px]" />
                </div>
                <div class="p-4 flex flex-col h-full">
                    <h2 class="text-xl font-semibold mb-4">Minhas Contas</h2>
                    <div v-if="accounts.length > 0" class="flex-1 overflow-y-auto">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-md shadow-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Conta</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Saldo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="account in accounts" :key="account.id">
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ account.id }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ account.account_type }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ account.title }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ formatCurrency(account.balance) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-gray-500 dark:text-gray-400">
                        Nenhuma conta encontrada.
                    </div>
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min p-4 flex flex-col">
                <h2 class="text-xl font-semibold mb-4">Últimas Transações</h2>
                <div v-if="transactions.length > 0" class="flex-1 overflow-y-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-md shadow-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Conta</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descrição</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="transaction in transactions" :key="transaction.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ transaction.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ transaction.account_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ transaction.type }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ transaction.description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">R$ {{ (transaction.amount / 100).toFixed(2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ new Date(transaction.created_at).toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' }) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-gray-500 dark:text-gray-400">
                    Nenhuma transação nos últimos 7 dias.
                </div>
            </div>
        </div>
    </AppLayout>
</template>