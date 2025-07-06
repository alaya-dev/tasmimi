<template>
    <Head title="Modifier un utilisateur" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier l'utilisateur</h2>
                <Link :href="route('admin.users.index')" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour à la liste
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': form.errors.name }"
                                    required
                                />
                                <div v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': form.errors.email }"
                                    required
                                />
                                <div v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="role" class="block text-sm font-medium text-gray-700">Rôle</label>
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': form.errors.role }"
                                    required
                                >
                                    <option value="">Sélectionner un rôle</option>
                                    <option v-for="role in roles" :key="role" :value="role">
                                        {{ getRoleLabel(role) }}
                                    </option>
                                </select>
                                <div v-if="form.errors.role" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.role }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Nouveau mot de passe (laisser vide pour ne pas changer)
                                </label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': form.errors.password }"
                                />
                                <div v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div class="flex items-center justify-end space-x-4">
                                <Link :href="route('admin.users.index')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Annuler
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Mise à jour...</span>
                                    <span v-else>Mettre à jour</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    user: Object,
    roles: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};

const getRoleLabel = (role) => {
    const labels = {
        'super_admin': 'Super Admin',
        'admin': 'Admin',
        'client': 'Client'
    };
    return labels[role] || role;
};
</script>

