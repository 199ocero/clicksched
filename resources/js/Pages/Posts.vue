<script setup lang="ts">
import BlueskyLogo from '@/Components/Platforms/Logos/BlueskyLogo.vue';
import PostStatusBadge from '@/Components/PostStatusBadge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CalendarOptions } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import FullCalendar from '@fullcalendar/vue3';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

import { Card, CardContent, CardHeader, CardTitle } from '@/shadcn/ui/card';

import {
    CommandEmpty,
    CommandGroup,
    CommandItem,
    CommandList,
} from '@/shadcn/ui/command';
import { Label } from '@/shadcn/ui/label';
import {
    TagsInput,
    TagsInputInput,
    TagsInputItem,
    TagsInputItemDelete,
    TagsInputItemText,
} from '@/shadcn/ui/tags-input';
import { FileClock } from 'lucide-vue-next';
import {
    ComboboxAnchor,
    ComboboxContent,
    ComboboxInput,
    ComboboxPortal,
    ComboboxRoot,
} from 'radix-vue';

const calendarRef = ref<InstanceType<typeof FullCalendar>>();

const statuses = [
    { value: 'draft', label: 'Draft' },
    { value: 'queued', label: 'Queued' },
    { value: 'running', label: 'Running' },
    { value: 'published', label: 'Published' },
    { value: 'scheduled', label: 'Scheduled' },
    { value: 'failed', label: 'Failed' },
];

const selectedStatus = ref<string[]>([]);
const statusOpen = ref(false);
const statusSearchTerm = ref('');

const filteredStatuses = computed(() =>
    statuses.filter((status) => !selectedStatus.value.includes(status.value)),
);

const platforms = [
    { value: 'bluesky', label: 'Bluesky' },
    { value: 'x(twitter)', label: 'X(Twitter)' },
];

const selectedPlatform = ref<string[]>([]);
const platformOpen = ref(false);
const platformSearchTerm = ref('');

const filteredPlatforms = computed(() =>
    platforms.filter(
        (platform) => !selectedPlatform.value.includes(platform.value),
    ),
);

interface Account {
    key: number;
    id: number;
    name: string;
    handle: string;
    profile_image_url: string;
    sociable_id: number;
    sociable_type: string;
    platform: string;
}

const accountProps = defineProps<{
    accounts: Account[];
}>();

const selectedAccount = ref<Account[]>([]);
const accountOpen = ref(false);
const accountSearchTerm = ref('');

const filteredAccounts = computed(() => {
    if (selectedPlatform.value.length === 0) {
        return accountProps.accounts.filter(
            (account) =>
                !selectedAccount.value.some(
                    (selected) => selected.id === account.id,
                ),
        );
    }

    return accountProps.accounts.filter(
        (account) =>
            !selectedAccount.value.some(
                (selected) => selected.id === account.id,
            ) && selectedPlatform.value.includes(account.platform),
    );
});

const removeAccount = (accountToRemove: Account) => {
    selectedAccount.value = selectedAccount.value.filter(
        (account) => account.id !== accountToRemove.id,
    );
};

watch(
    selectedPlatform,
    (newPlatforms) => {
        if (newPlatforms.length === 0) {
            selectedAccount.value = [];
        } else {
            selectedAccount.value = selectedAccount.value.filter((account) =>
                newPlatforms.includes(account.platform),
            );
        }
    },
    { immediate: true, deep: true },
);

interface Post {
    platform: string;
    platform_label: string;
    account_id: number;
    content: string;
    status: string;
    post_id: string | null;
    published_at: string | null;
    created_at: string;
    updated_at: string;
}

const calendarOptions = ref<CalendarOptions>({
    plugins: [dayGridPlugin, timeGridPlugin],
    initialView: 'dayGridMonth',
    height: '100%',
    headerToolbar: {
        start: 'prev,next today',
        center: 'title',
        end: 'dayGridYear,dayGridMonth,dayGridWeek',
    },
    events: async function (info, successCallback, failureCallback) {
        try {
            const params = {
                start_date: info.start.toISOString().split('T')[0],
                end_date: info.end.toISOString().split('T')[0],
                statuses:
                    selectedStatus.value.length > 0
                        ? selectedStatus.value
                        : null,
                platforms:
                    selectedPlatform.value.length > 0
                        ? selectedPlatform.value
                        : null,
                accounts: selectedAccount.value.map((account) => account.id),
            };

            const response = await axios.get('/api/posts', { params });

            const formattedEvents = response.data.data.map((post: Post) => ({
                title: post.content,
                start: post.published_at || post.updated_at,
                end: post.published_at || post.updated_at,
                allDay: false,
                extendedProps: {
                    post_id: post.post_id,
                    status: post.status,
                    account_id: post.account_id,
                    platform: post.platform,
                    platform_label: post.platform_label,
                    published_at: post.published_at || post.updated_at,
                },
            }));

            successCallback(formattedEvents);
        } catch (error) {
            if (axios.isAxiosError(error)) {
                const errorMessage =
                    error.response?.data?.message || 'Failed to fetch posts.';
                failureCallback(new Error(errorMessage));
            } else if (error instanceof Error) {
                failureCallback(error);
            } else {
                failureCallback(new Error('An unknown error occurred.'));
            }
        }
    },
});

watch(
    selectedStatus,
    () => {
        const calendarApi = calendarRef.value?.getApi();
        if (calendarApi) {
            calendarApi.refetchEvents();
        }
    },
    { immediate: true, deep: true },
);

watch(
    selectedPlatform,
    () => {
        const calendarApi = calendarRef.value?.getApi();
        if (calendarApi) {
            calendarApi.refetchEvents();
        }
    },
    { immediate: true, deep: true },
);

watch(
    selectedAccount,
    () => {
        const calendarApi = calendarRef.value?.getApi();
        if (calendarApi) {
            calendarApi.refetchEvents();
        }
    },
    { immediate: true, deep: true },
);

const formattedTime = (time: string) => {
    const date = new Date(time);
    let hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12;
    return `${hours.toString().padStart(2, '0')}:${minutes} ${ampm}`;
};
</script>

<template>
    <Head title="Calendar" />
    <AuthenticatedLayout>
        <div class="flex flex-grow flex-col gap-5 xl:flex-row">
            <!-- Sidebar for Filters -->
            <Card class="flex w-full flex-col gap-2 p-5 xl:w-1/5">
                <h3
                    class="text-base font-semibold text-gray-700 dark:text-gray-300"
                >
                    Filters
                </h3>
                <div>
                    <Label for="status"> Platform </Label>
                    <TagsInput
                        id="status"
                        class="w-full gap-0 bg-white px-0 dark:bg-zinc-950"
                        :model-value="selectedPlatform"
                    >
                        <div class="flex flex-wrap items-center gap-2 px-3">
                            <TagsInputItem
                                v-for="item in selectedPlatform"
                                :key="item"
                                :value="item"
                            >
                                <TagsInputItemText />
                                <TagsInputItemDelete />
                            </TagsInputItem>
                        </div>

                        <ComboboxRoot
                            v-model="selectedStatus"
                            v-model:open="platformOpen"
                            v-model:search-term="platformSearchTerm"
                            class="w-full"
                        >
                            <ComboboxAnchor as-child>
                                <ComboboxInput
                                    class="bg-white px-3 dark:bg-zinc-950"
                                    placeholder="Select platform..."
                                    as-child
                                >
                                    <TagsInputInput
                                        class="w-full px-3"
                                        :class="
                                            selectedPlatform.length > 0
                                                ? 'mt-2'
                                                : ''
                                        "
                                        @keydown.enter.prevent
                                        @focus="platformOpen = true"
                                    />
                                </ComboboxInput>
                            </ComboboxAnchor>

                            <ComboboxPortal>
                                <ComboboxContent>
                                    <CommandList
                                        position="popper"
                                        class="mt-2 w-[--radix-popper-anchor-width] rounded-md border bg-popover text-popover-foreground shadow-md outline-none data-[state=platformOpen]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=platformOpen]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=platformOpen]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
                                    >
                                        <CommandEmpty />
                                        <CommandGroup>
                                            <CommandItem
                                                v-for="platform in filteredPlatforms"
                                                :key="platform.value"
                                                :value="platform.value"
                                                @select.prevent="
                                                    (ev) => {
                                                        if (
                                                            typeof ev.detail
                                                                .value ===
                                                            'string'
                                                        ) {
                                                            platformSearchTerm =
                                                                '';
                                                            selectedPlatform.push(
                                                                ev.detail.value,
                                                            );
                                                        }

                                                        if (
                                                            filteredPlatforms.length ===
                                                            0
                                                        ) {
                                                            platformOpen = false;
                                                        }
                                                    }
                                                "
                                            >
                                                {{ platform.label }}
                                            </CommandItem>
                                        </CommandGroup>
                                    </CommandList>
                                </ComboboxContent>
                            </ComboboxPortal>
                        </ComboboxRoot>
                    </TagsInput>
                </div>
                <div>
                    <Label for="status"> Accounts </Label>
                    <TagsInput
                        id="status"
                        class="w-full gap-0 bg-white px-0 dark:bg-zinc-950"
                        :model-value="selectedAccount"
                    >
                        <div class="flex flex-wrap items-center gap-2 px-3">
                            <TagsInputItem
                                v-for="item in selectedAccount"
                                :key="item.id"
                                :value="item.name || item.handle"
                            >
                                <TagsInputItemText />
                                <TagsInputItemDelete
                                    @click="removeAccount(item)"
                                />
                            </TagsInputItem>
                        </div>

                        <ComboboxRoot
                            v-model="selectedAccount"
                            v-model:open="accountOpen"
                            v-model:search-term="accountSearchTerm"
                            class="w-full"
                        >
                            <ComboboxAnchor as-child>
                                <ComboboxInput
                                    class="bg-white px-3 dark:bg-zinc-950"
                                    placeholder="Select account..."
                                    as-child
                                >
                                    <TagsInputInput
                                        class="w-full px-3"
                                        :class="
                                            selectedAccount.length > 0
                                                ? 'mt-2'
                                                : ''
                                        "
                                        @keydown.enter.prevent
                                        @focus="accountOpen = true"
                                    />
                                </ComboboxInput>
                            </ComboboxAnchor>

                            <ComboboxPortal>
                                <ComboboxContent>
                                    <CommandList
                                        position="popper"
                                        class="mt-2 w-[--radix-popper-anchor-width] rounded-md border bg-popover text-popover-foreground shadow-md outline-none data-[state=accountOpen]:animate-in data-[state=closed]:animate-out data-[state=accountOpen]:fade-in-0 data-[state=closed]:fade-out-0 data-[state=accountOpen]:zoom-in-95 data-[state=closed]:zoom-out-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
                                    >
                                        <CommandEmpty />
                                        <CommandGroup>
                                            <CommandItem
                                                v-for="account in filteredAccounts"
                                                :key="account.id"
                                                :value="account.id"
                                                @select.prevent="
                                                    (ev) => {
                                                        if (ev.detail.value) {
                                                            const selectedAccountObject =
                                                                filteredAccounts.find(
                                                                    (acc) =>
                                                                        acc.id ===
                                                                        Number(
                                                                            ev
                                                                                .detail
                                                                                .value,
                                                                        ),
                                                                );

                                                            if (
                                                                selectedAccountObject
                                                            ) {
                                                                accountSearchTerm =
                                                                    '';
                                                                selectedAccount.push(
                                                                    selectedAccountObject,
                                                                );
                                                            }
                                                        }

                                                        if (
                                                            filteredAccounts.length ===
                                                            0
                                                        ) {
                                                            platformOpen = false;
                                                        }
                                                    }
                                                "
                                            >
                                                {{
                                                    account.name ||
                                                    account.handle
                                                }}
                                            </CommandItem>
                                        </CommandGroup>
                                    </CommandList>
                                </ComboboxContent>
                            </ComboboxPortal>
                        </ComboboxRoot>
                    </TagsInput>
                </div>
                <div>
                    <Label for="status"> Status </Label>
                    <TagsInput
                        id="status"
                        class="w-full gap-0 bg-white px-0 dark:bg-zinc-950"
                        :model-value="selectedStatus"
                    >
                        <div class="flex flex-wrap items-center gap-2 px-3">
                            <TagsInputItem
                                v-for="item in selectedStatus"
                                :key="item"
                                :value="item"
                            >
                                <TagsInputItemText />
                                <TagsInputItemDelete />
                            </TagsInputItem>
                        </div>

                        <ComboboxRoot
                            v-model="selectedStatus"
                            v-model:open="statusOpen"
                            v-model:search-term="statusSearchTerm"
                            class="w-full"
                        >
                            <ComboboxAnchor as-child>
                                <ComboboxInput
                                    class="bg-white px-3 dark:bg-zinc-950"
                                    placeholder="Select status..."
                                    as-child
                                >
                                    <TagsInputInput
                                        class="w-full px-3"
                                        :class="
                                            selectedStatus.length > 0
                                                ? 'mt-2'
                                                : ''
                                        "
                                        @keydown.enter.prevent
                                        @focus="statusOpen = true"
                                    />
                                </ComboboxInput>
                            </ComboboxAnchor>

                            <ComboboxPortal>
                                <ComboboxContent>
                                    <CommandList
                                        position="popper"
                                        class="mt-2 w-[--radix-popper-anchor-width] rounded-md border bg-popover text-popover-foreground shadow-md outline-none data-[state=statusOpen]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=statusOpen]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=statusOpen]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
                                    >
                                        <CommandEmpty />
                                        <CommandGroup>
                                            <CommandItem
                                                v-for="status in filteredStatuses"
                                                :key="status.value"
                                                :value="status.value"
                                                @select.prevent="
                                                    (ev) => {
                                                        if (
                                                            typeof ev.detail
                                                                .value ===
                                                            'string'
                                                        ) {
                                                            statusSearchTerm =
                                                                '';
                                                            selectedStatus.push(
                                                                ev.detail.value,
                                                            );
                                                        }

                                                        if (
                                                            filteredStatuses.length ===
                                                            0
                                                        ) {
                                                            statusOpen = false;
                                                        }
                                                    }
                                                "
                                            >
                                                {{ status.label }}
                                            </CommandItem>
                                        </CommandGroup>
                                    </CommandList>
                                </ComboboxContent>
                            </ComboboxPortal>
                        </ComboboxRoot>
                    </TagsInput>
                </div>
            </Card>

            <FullCalendar
                ref="calendarRef"
                :options="calendarOptions"
                class="full-calendar-theme w-full"
            >
                <template v-slot:eventContent="arg">
                    <Card
                        class="w-full p-2"
                        :class="{
                            'border-l-1 border-gray-500 md:border-l-4':
                                arg.event.extendedProps.status === 'draft',
                            'border-l-1 border-blue-500 md:border-l-4':
                                arg.event.extendedProps.status === 'queued',
                            'border-l-1 border-yellow-500 md:border-l-4':
                                arg.event.extendedProps.status === 'running',
                            'border-l-1 border-green-500 md:border-l-4':
                                arg.event.extendedProps.status === 'published',
                            'border-l-1 border-purple-500 md:border-l-4':
                                arg.event.extendedProps.status === 'scheduled',
                            'border-l-1 border-red-500 md:border-l-4':
                                arg.event.extendedProps.status === 'failed',
                        }"
                    >
                        <CardHeader class="hidden p-0 pb-2 md:block">
                            <CardTitle
                                class="flex gap-1 text-sm font-semibold text-zinc-900 dark:text-zinc-100"
                            >
                                <BlueskyLogo
                                    v-if="
                                        arg.event.extendedProps.platform ===
                                        'bluesky'
                                    "
                                    class="h-4 w-4"
                                />
                                {{ arg.event.extendedProps.platform_label }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="p-0">
                            <p
                                class="line-clamp-2 whitespace-normal break-words text-xs text-zinc-600 dark:text-zinc-400"
                            >
                                {{ arg.event.title }}
                            </p>
                            <div
                                class="flex flex-col justify-start gap-2 p-0 pt-2 text-xs text-zinc-500 dark:text-zinc-500"
                            >
                                <PostStatusBadge
                                    class="hidden md:block"
                                    :status="arg.event.extendedProps.status"
                                />
                                <div class="hidden gap-1 md:flex">
                                    <FileClock
                                        class="h-4 w-4 text-zinc-500 dark:text-zinc-400"
                                    />
                                    <span>{{
                                        formattedTime(arg.event.start)
                                    }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </template>
            </FullCalendar>
        </div>
    </AuthenticatedLayout>
</template>
