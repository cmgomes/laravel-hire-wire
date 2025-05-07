<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
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
} from '@/components/ui/select'
import { LoaderCircle } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const authToken = localStorage.getItem('authToken');
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Crie sua nova conta',
        href: '/new-account',
    },
];
const selectedAccountTypeId = ref<number | null>(null);
const accountTypes = ref<AccountTypeOption[]>([]);

interface AccountTypeOption {
    id: number;
    name: string;
}

const form = useForm({
    account_type_id: '',
    title: '',
    description: '',
});

const handleAccountTypeChange = (value: string) => {
    selectedAccountTypeId.value = parseInt(value);
    form.account_type_id = value;
};
const submit = () => {
    form.post(route('api.new-account.store', {
        headers: {
            'Authorization': `Bearer ${authToken}`,
            'Accept': 'application/json',
        },
    }), {
        onSuccess: (response) => {
            alert(response.message || 'Sua conta foi criada com sucesso!');
            router.reload();
        },
        onError: (errors) => {
            console.error('Erro ao criar sua conta:', errors);
            alert(response.message || 'Ocorreu um erro durante o depósito.');
        },
    });
};

onMounted(async () => {
    let response = null;
    try {
        response = await fetch('/api/account-types', {
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
            },
        });
        if (response.ok) {
            accountTypes.value = await response.json();
        } else {
            console.error('Erro ao buscar tipos de conta');
        }
    } catch (error) {
        console.error('Erro ao buscar tipos de conta:', error);
        console.log(response)
    }
});

const goToDashboard = () => {
    router.push(route('dashboard'));
};
</script>

<template>
    <Head title="Crie sua Conta" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-full max-h-[500px]">
                <div class="relative rounded-xl md:min-h-min border border-orange-700 p-4">
                    <form @submit.prevent="submit" class="flex flex-col gap-6">
                        <div class="grid gap-6">
                            <div class="grid gap-2">
                                <Label for="account_type_id">Tipo de Conta</Label>
                                <Select v-model="selectedAccountTypeId" @update:modelValue="handleAccountTypeChange($event)">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Selecione o tipo de conta" :value="selectedAccountTypeId" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="type in accountTypes"
                                            :key="type.id"
                                            :value="type.id"
                                        >
                                            {{ type.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>

                                <InputError :message="form.errors.account_type_id" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="title">Título da Conta</Label>
                                <Input id="title" type="text" required :tabindex="2" v-model="form.title" placeholder="Ex: Conta Corrente Principal, Poupança Viagem" />
                                <InputError :message="form.errors.title" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="description">Descrição (Opcional)</Label>
                                <Input id="description" type="text" :tabindex="3" v-model="form.description" placeholder="Descrição curta da conta (opcional)" />
                                <InputError :message="form.errors.description" />
                            </div>

                            <Button type="submit" class="mt-2 w-full" tabindex="4" :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                Criar Conta
                            </Button>
                        </div>
                    </form>
                </div>
                <div class="relative rounded-xl md:min-h-min col-span-2">
                    <img src="http://localhost:9000/images/cartao_acc.png" class="max-h-[500px]" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
