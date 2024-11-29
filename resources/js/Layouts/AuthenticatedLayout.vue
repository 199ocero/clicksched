<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Button } from '@/shadcn/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/shadcn/ui/dropdown-menu';
import { Sheet, SheetContent, SheetTrigger } from '@/shadcn/ui/sheet';
import { Toaster } from '@/shadcn/ui/toast';
import { useToast } from '@/shadcn/ui/toast/use-toast';
import { Link, usePage } from '@inertiajs/vue3';
import { CircleUser, LogOut, Menu, Settings } from 'lucide-vue-next';
import { onMounted, watch } from 'vue';

interface Route {
    name: string;
    href: string;
    routeName: string;
}

const navigationRoutes: Route[] = [
    { name: 'Dashboard', href: route('dashboard'), routeName: 'dashboard' },
];

const currentRoute = route().current();

const { toast } = useToast();
const page = usePage();

const showFlashToast = () => {
    const flash = page.props.flash as Record<string, any>;

    if (flash.success && typeof flash.success === 'object') {
        toast({
            title: flash.success.title ?? 'Success',
            description: flash.success.message ?? 'Successfully completed action.',
            duration: flash.success.duration ?? 5000,
            variant: 'default',
        });
    }

    if (flash.error && typeof flash.error === 'object') {
        toast({
            title: flash.error.title ?? 'Error',
            description: flash.error.message ?? 'An error occurred.',
            duration: flash.error.duration ?? 5000,
            variant: 'destructive',
        });
    }

    if (flash.info && typeof flash.info === 'object') {
        toast({
            title: flash.info.title ?? 'Information',
            description: flash.info.message ?? 'Information message.',
            duration: flash.info.duration ?? 5000,
            variant: 'default',
        });
    }

    if (flash.message && typeof flash.message === 'object') {
        toast({
            title: flash.message.title ?? 'Message',
            description: flash.message.message ?? 'Message.',
            duration: flash.message.duration ?? 5000,
            variant: 'default',
        });
    }
};

watch(
    () => page.props.flash,
    () => {
        showFlashToast();
    },
);

onMounted(() => {
    showFlashToast();
});
</script>

<template>
    <Toaster />
    <div class="flex min-h-screen w-full flex-col">
        <header
            class="sticky top-0 flex h-16 items-center gap-4 border-b bg-background px-4 md:px-6"
        >
            <nav
                class="hidden flex-col gap-6 text-lg font-medium md:flex md:flex-row md:items-center md:gap-5 md:text-sm lg:gap-6"
            >
                <Link
                    :href="route('home')"
                    class="flex items-center gap-2 text-lg font-semibold md:text-base"
                >
                    <ApplicationLogo class="h-8 w-8" />
                    <span>{{ $page.props.app.name }}</span>
                </Link>

                <Link
                    v-for="navItem in navigationRoutes"
                    :key="navItem.name"
                    :href="navItem.href"
                    :class="[
                        currentRoute === navItem.routeName
                            ? 'text-foreground'
                            : 'text-muted-foreground',
                        'transition-colors hover:text-foreground',
                    ]"
                >
                    {{ navItem.name }}
                </Link>
            </nav>

            <Sheet>
                <SheetTrigger as-child>
                    <Button
                        variant="outline"
                        size="icon"
                        class="shrink-0 md:hidden"
                    >
                        <Menu class="h-5 w-5" />
                        <span class="sr-only">Toggle navigation menu</span>
                    </Button>
                </SheetTrigger>
                <SheetContent side="left">
                    <nav class="grid gap-6 text-lg font-medium">
                        <Link
                            :href="route('home')"
                            class="flex items-center gap-2 text-lg font-semibold"
                        >
                            <ApplicationLogo class="h-8 w-8" />
                            <span>{{ $page.props.app.name }}</span>
                        </Link>

                        <Link
                            v-for="navItem in navigationRoutes"
                            :key="navItem.name"
                            :href="navItem.href"
                            :class="[
                                currentRoute === navItem.routeName
                                    ? 'text-foreground'
                                    : 'text-muted-foreground',
                                'transition-colors hover:text-foreground',
                            ]"
                        >
                            {{ navItem.name }}
                        </Link>
                    </nav>
                </SheetContent>
            </Sheet>

            <div class="flex w-full items-center justify-end gap-2">
                <ThemeToggle />
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button
                            variant="secondary"
                            size="icon"
                            class="rounded-full"
                        >
                            <CircleUser class="h-5 w-5" />
                            <span class="sr-only">Toggle user menu</span>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuLabel>My Account</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem>
                            <Link
                                :href="route('profile.edit')"
                                class="flex w-full cursor-pointer items-center gap-3"
                            >
                                <Settings class="h-4 w-4" />
                                <span>Settings</span>
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex w-full cursor-pointer items-center gap-3"
                            >
                                <LogOut class="h-4 w-4" />
                                <span>Logout</span>
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </header>

        <main
            class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-4 p-4 md:gap-8 md:p-8"
        >
            <slot />
        </main>
    </div>
</template>
