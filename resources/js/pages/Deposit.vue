<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { LoaderCircle } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Depositar',
        href: '/deposit',
    },
];

interface AccountOption {
    id: number;
    title: string;
}

const accounts = ref<AccountOption[]>([]);
const selectedAccountId = ref<number | null>(null);
const authToken = localStorage.getItem('authToken');

const form = useForm({
    account_id: '',
    amount: ref(0),
});

const submit = () => {
    form.post(route('api.deposit'), {
        headers: {
            'Authorization': `Bearer ${authToken}`,
            'Accept': 'application/json',
        },
        onSuccess: (response) => {
            alert(response.message || 'Depósito realizado com sucesso!');
            router.reload();
        },
        onError: (errors) => {
            console.error('Erros ao realizar o depósito:', errors);
            alert(response.message || 'Ocorreu um erro durante o depósito.');
        },
    });
};

onMounted(async () => {
    try {
        const response = await fetch('/api/user/accounts', {
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
            },
        });
        if (response.ok) {
            accounts.value = await response.json();
        } else {
            console.error('Erro ao buscar contas');
        }
    } catch (error) {
        console.error('Erro ao buscar contas:', error);
    }
});

const filterAmountInput = (event: Event) => {
    const inputElement = event.target as HTMLInputElement;
    const newValue = inputElement.value.replace(/[^0-9]+/g, '');
    form.amount = parseInt(newValue);
};
</script>

<template>
    <Head title="Depositar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-full max-h-[500px]">
                <div class="relative rounded-xl md:min-h-min border border-orange-700 p-4">
                    <form @submit.prevent="submit" class="flex flex-col gap-6">
                        <div class="grid gap-6">
                            <div class="grid gap-2">
                                <Label for="account_id">Depositar em</Label>
                                <Select v-model="selectedAccountId" @update:modelValue="form.account_id = $event">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Selecione a conta" :value="selectedAccountId" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="account in accounts"
                                            :key="account.id"
                                            :value="account.id"
                                        >
                                            {{ account.title }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.account_id" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="amount">Valor do Depósito</Label>
                                <Input
                                    id="amount"
                                    type="text"
                                    required
                                    placeholder="0,00"
                                    @input="filterAmountInput"
                                />
                                <InputError :message="form.errors.amount" />
                            </div>

                            <Button type="submit" class="mt-2 w-full" :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                Depositar
                            </Button>
                        </div>
                    </form>
                </div>
                <div class="relative rounded-xl md:min-h-min col-span-2 p-4">
                    <img src="http://localhost:9000/images/caixa_acc_bank.png" class="max-h-[470px]" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
