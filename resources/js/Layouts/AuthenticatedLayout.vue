<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Button } from '@/shadcn/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/shadcn/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/shadcn/ui/dropdown-menu';
import { Separator } from '@/shadcn/ui/separator';
import { Sheet, SheetContent, SheetTrigger } from '@/shadcn/ui/sheet';
import { Toaster } from '@/shadcn/ui/toast';
import { useToast } from '@/shadcn/ui/toast/use-toast';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bell,
    CircleUser,
    FilePlus2,
    Files,
    LayoutDashboard,
    Library,
    LogOut,
    Menu,
    UserRoundCog,
    Users,
} from 'lucide-vue-next';
import { onMounted, watch } from 'vue';

interface Route {
    name: string;
    href: string;
    routeName: string;
    icon: any;
}

const dashboardRoutes: Route[] = [
    {
        name: 'Dashboard',
        href: route('dashboard'),
        routeName: 'dashboard',
        icon: LayoutDashboard,
    },
];

const mainNavigationRoutes: Route[] = [
    {
        name: 'New Post',
        href: route('new-post.index'),
        routeName: 'new-post.index',
        icon: FilePlus2,
    },
    {
        name: 'Posts',
        href: route('posts.index'),
        routeName: 'posts.index',
        icon: Files,
    },
    {
        name: 'Media',
        href: route('media.index'),
        routeName: 'media.index',
        icon: Library,
    },
];

const accountManagementRoutes: Route[] = [
    {
        name: 'Accounts',
        href: route('accounts.index'),
        routeName: 'accounts.index',
        icon: Users,
    },
    {
        name: 'Profile',
        href: route('profile.edit'),
        routeName: 'profile.edit',
        icon: UserRoundCog,
    },
];

const currentRoute = route().current();

const { toast } = useToast();
const page = usePage();

const showFlashToast = () => {
    const flash = page.props.flash as Record<string, any>;

    if (flash.success && typeof flash.success === 'object') {
        toast({
            title: flash.success.title ?? 'Success',
            description:
                flash.success.message ?? 'Successfully completed action.',
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
    <div class="grid min-h-screen w-full lg:grid-cols-[280px_1fr]">
        <div
            class="sticky top-0 hidden h-screen overflow-y-auto border-r bg-muted/40 lg:block"
        >
            <div class="flex h-full max-h-screen flex-col gap-2">
                <div
                    class="sticky top-0 z-10 flex h-14 items-center border-b bg-muted/40 px-4 lg:h-[60px] lg:px-6"
                >
                    <a href="/" class="flex items-center gap-2 font-semibold">
                        <ApplicationLogo class="h-8 w-8" />
                        <span class="">
                            {{ $page.props.app.name }}
                        </span>
                    </a>
                    <Button
                        variant="outline"
                        size="icon"
                        class="ml-auto h-8 w-8"
                    >
                        <Bell class="h-4 w-4" />
                        <span class="sr-only">Toggle notifications</span>
                    </Button>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <nav
                        class="grid items-start px-2 text-sm font-medium lg:px-4"
                    >
                        <Link
                            v-for="navItem in dashboardRoutes"
                            :key="navItem.name"
                            :href="navItem.href"
                            :class="[
                                currentRoute === navItem.routeName
                                    ? 'bg-muted text-foreground'
                                    : 'text-muted-foreground',
                                'flex items-center gap-3 rounded-lg px-3 py-2 transition-all hover:text-foreground',
                            ]"
                        >
                            <component
                                :is="navItem.icon"
                                class="mr-2 h-4 w-4"
                            />
                            {{ navItem.name }}
                        </Link>
                        <Separator class="my-2" />
                        <Link
                            v-for="navItem in mainNavigationRoutes"
                            :key="navItem.name"
                            :href="navItem.href"
                            :class="[
                                currentRoute === navItem.routeName
                                    ? 'bg-muted text-foreground'
                                    : 'text-muted-foreground',
                                'flex items-center gap-3 rounded-lg px-3 py-2 transition-all hover:text-foreground',
                            ]"
                        >
                            <component
                                :is="navItem.icon"
                                class="mr-2 h-4 w-4"
                            />
                            {{ navItem.name }}
                        </Link>
                        <Separator class="my-2" />
                        <Link
                            v-for="navItem in accountManagementRoutes"
                            :key="navItem.name"
                            :href="navItem.href"
                            :class="[
                                currentRoute === navItem.routeName
                                    ? 'bg-muted text-foreground'
                                    : 'text-muted-foreground',
                                'flex items-center gap-3 rounded-lg px-3 py-2 transition-all hover:text-foreground',
                            ]"
                        >
                            <component
                                :is="navItem.icon"
                                class="mr-2 h-4 w-4"
                            />
                            {{ navItem.name }}
                        </Link>
                    </nav>
                </div>
                <div class="sticky bottom-0 mt-auto p-4">
                    <Card>
                        <CardHeader class="p-2 pt-0 md:p-4">
                            <CardTitle>Upgrade to Pro</CardTitle>
                            <CardDescription>
                                Unlock all features and get unlimited access to
                                our support team.
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="p-2 pt-0 md:p-4 md:pt-0">
                            <Button size="sm" class="w-full"> Upgrade </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <header
                class="sticky top-0 z-10 flex h-14 items-center gap-4 border-b bg-[#F8F8FA] px-4 dark:bg-[#1E1E23] lg:h-[60px] lg:px-6"
            >
                <Sheet>
                    <SheetTrigger as-child>
                        <Button
                            variant="outline"
                            size="icon"
                            class="shrink-0 lg:hidden"
                        >
                            <Menu class="h-5 w-5" />
                            <span class="sr-only">Toggle navigation menu</span>
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="left" class="flex flex-col">
                        <nav class="mt-2 grid gap-2 text-lg font-medium">
                            <Link
                                v-for="navItem in dashboardRoutes"
                                :key="navItem.name"
                                :href="navItem.href"
                                :class="[
                                    currentRoute === navItem.routeName
                                        ? 'bg-muted text-foreground'
                                        : 'text-muted-foreground',
                                    'mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2',
                                ]"
                            >
                                <component
                                    :is="navItem.icon"
                                    class="mr-2 h-6 w-6"
                                />
                                {{ navItem.name }}
                            </Link>
                            <Separator />
                            <Link
                                v-for="navItem in mainNavigationRoutes"
                                :key="navItem.name"
                                :href="navItem.href"
                                :class="[
                                    currentRoute === navItem.routeName
                                        ? 'bg-muted text-foreground'
                                        : 'text-muted-foreground',
                                    'mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2',
                                ]"
                            >
                                <component
                                    :is="navItem.icon"
                                    class="mr-2 h-6 w-6"
                                />
                                {{ navItem.name }}
                            </Link>
                            <Separator />
                            <Link
                                v-for="navItem in accountManagementRoutes"
                                :key="navItem.name"
                                :href="navItem.href"
                                :class="[
                                    currentRoute === navItem.routeName
                                        ? 'bg-muted text-foreground'
                                        : 'text-muted-foreground',
                                    'mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2',
                                ]"
                            >
                                <component
                                    :is="navItem.icon"
                                    class="mr-2 h-6 w-6"
                                />
                                {{ navItem.name }}
                            </Link>
                        </nav>
                        <div class="mt-auto">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Upgrade to Pro</CardTitle>
                                    <CardDescription>
                                        Unlock all features and get unlimited
                                        access to our support team.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <Button size="sm" class="w-full">
                                        Upgrade
                                    </Button>
                                </CardContent>
                            </Card>
                        </div>
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
                                    <UserRoundCog class="h-4 w-4" />
                                    <span>Profile</span>
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
                class="flex flex-1 flex-col gap-4 overflow-y-auto p-4 lg:gap-6 lg:p-6"
            >
                <slot />
            </main>
        </div>
    </div>
</template>
