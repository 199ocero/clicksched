<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/shadcn/ui/avatar';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/shadcn/ui/tooltip';
import { SocialAccount } from '@/types/socialAccounts';
import { Check } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    selectedAccounts: SocialAccount[];
    htmlContent: string;
}>();

const selectedAccountId = ref<number | null>(
    props.selectedAccounts.length > 0 ? props.selectedAccounts[0].id : null,
);

watch(
    () => props.selectedAccounts,
    (newAccounts) => {
        if (
            !newAccounts.some(
                (account) => account.id === selectedAccountId.value,
            )
        ) {
            selectedAccountId.value =
                newAccounts.length > 0 ? newAccounts[0].id : null;
        }
    },
    { immediate: true, deep: true },
);

const selectAccount = (account: SocialAccount) => {
    selectedAccountId.value = account.id;
};

const getSelectedAccount = computed(() => {
    return props.selectedAccounts.find(
        (account) => account.id === selectedAccountId.value,
    );
});

const processedHtmlContent = computed(() => {
    const maxLength = 300;
    
    // Create a temporary div to parse the HTML
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = props.htmlContent;

    // Helper function to get total text content
    const getTotalTextContent = (node: Node): string => {
        if (node.nodeType === Node.TEXT_NODE) {
            return node.textContent || '';
        }
        
        if (node.nodeType === Node.ELEMENT_NODE) {
            const element = node as Element;
            
            // Special handling for BR tags
            if (element.tagName === 'BR') {
                return ' ';
            }
            
            return Array.from(element.childNodes)
                .map(child => getTotalTextContent(child))
                .join('');
        }
        
        return '';
    };

    // Recursive truncation function
    const truncateContent = (node: Node, remainingChars: number): { html: string, charsUsed: number } => {
        if (remainingChars <= 0) {
            return { html: '', charsUsed: 0 };
        }

        // Text node handling
        if (node.nodeType === Node.TEXT_NODE) {
            const text = node.textContent || '';
            const truncatedText = text.slice(0, remainingChars);
            return {
                html: truncatedText,
                charsUsed: truncatedText.length
            };
        }

        // Element node handling
        if (node.nodeType === Node.ELEMENT_NODE) {
            const element = node as Element;
            
            // BR tag handling - count as 1 character
            if (element.tagName === 'BR') {
                return { 
                    html: remainingChars > 0 ? '<br>' : '', 
                    charsUsed: remainingChars > 0 ? 1 : 0 
                };
            }

            // Create opening tag with attributes
            let truncatedHtml = `<${element.tagName}`;
            for (const attr of element.attributes) {
                truncatedHtml += ` ${attr.name}="${attr.value}"`;
            }
            truncatedHtml += '>';

            let remainingLength = remainingChars;
            const childContents: string[] = [];

            // Process child nodes
            for (const childNode of Array.from(element.childNodes)) {
                if (remainingLength <= 0) break;

                const { html: childHtml, charsUsed } = truncateContent(childNode, remainingLength);
                childContents.push(childHtml);
                remainingLength -= charsUsed;
            }

            truncatedHtml += childContents.join('');
            truncatedHtml += `</${element.tagName}>`;

            return {
                html: truncatedHtml,
                charsUsed: remainingChars - remainingLength
            };
        }

        return { html: '', charsUsed: 0 };
    };

    // Perform truncation
    const { html: truncatedHtml, charsUsed } = truncateContent(tempDiv, maxLength);

    // Check if there's more content
    const totalTextContent = getTotalTextContent(tempDiv);
    
    let finalHtml = truncatedHtml;
    if (charsUsed < totalTextContent.length) {
        finalHtml += `<span class="text-gray-500">...</span>`;
    }

    return finalHtml;
});
</script>

<template>
    <div class="flex flex-wrap gap-1">
        <template v-for="account in selectedAccounts">
            <template v-if="account.platform_identifier === 'bluesky'">
                <TooltipProvider :delay-duration="0">
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <div
                                class="relative inline-block cursor-pointer"
                                @click="selectAccount(account)"
                            >
                                <Avatar class="h-8 w-8 rounded-full">
                                    <AvatarImage
                                        :src="account.profile_image_url"
                                        :alt="account.name"
                                    />
                                    <AvatarFallback>
                                        {{
                                            account.name.charAt(0).toUpperCase()
                                        }}
                                    </AvatarFallback>
                                </Avatar>

                                <div
                                    v-if="selectedAccountId === account.id"
                                    class="absolute bottom-0 right-0 rounded-full bg-blue-600 p-1 dark:bg-blue-500"
                                >
                                    <Check class="h-2 w-2 text-white" />
                                </div>
                            </div>
                        </TooltipTrigger>
                        <TooltipContent>
                            <span>{{ account.name || account.handle }}</span>
                        </TooltipContent>
                    </Tooltip>
                </TooltipProvider>
            </template>
        </template>
    </div>

    <div
        v-if="getSelectedAccount"
        class="grid w-full gap-3 rounded-md border border-zinc-200 p-5 dark:border-zinc-800"
    >
        <div class="flex min-w-0 items-center gap-2">
            <Avatar class="h-10 w-10 rounded-full">
                <AvatarImage
                    :src="getSelectedAccount!.profile_image_url"
                    :alt="getSelectedAccount!.name"
                />
                <AvatarFallback>
                    {{ getSelectedAccount!.name.charAt(0).toUpperCase }}
                </AvatarFallback>
            </Avatar>
            <div class="flex min-w-0 flex-col">
                <p class="min-w-0 truncate text-base font-semibold">
                    {{ getSelectedAccount!.name || getSelectedAccount!.handle }}
                </p>
                <p class="min-w-0 truncate text-sm text-zinc-500">
                    @{{ getSelectedAccount!.handle }}
                </p>
            </div>
        </div>
        <div
            v-html="processedHtmlContent"
            class="w-full max-w-full break-all text-sm"
        ></div>
        <div class="flex justify-between gap-2">
            <div class="social-button">
                <svg
                    fill="none"
                    width="22"
                    viewBox="0 0 24 24"
                    height="22"
                    style="color: rgb(111, 134, 159); pointer-events: none"
                >
                    <path
                        fill="hsl(211, 20%, 53%)"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M2.002 6a3 3 0 0 1 3-3h14a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H12.28l-4.762 2.858A1 1 0 0 1 6.002 21v-2h-1a3 3 0 0 1-3-3V6Zm3-1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h2a1 1 0 0 1 1 1v1.234l3.486-2.092a1 1 0 0 1 .514-.142h7a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-14Z"
                    ></path>
                </svg>
            </div>
            <div class="social-button">
                <svg
                    fill="none"
                    width="22"
                    viewBox="0 0 24 24"
                    height="22"
                    style="color: rgb(111, 134, 159)"
                >
                    <path
                        fill="hsl(211, 20%, 53%)"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M17.957 2.293a1 1 0 1 0-1.414 1.414L17.836 5H6a3 3 0 0 0-3 3v3a1 1 0 1 0 2 0V8a1 1 0 0 1 1-1h11.836l-1.293 1.293a1 1 0 0 0 1.414 1.414l2.47-2.47a1.75 1.75 0 0 0 0-2.474l-2.47-2.47ZM20 12a1 1 0 0 1 1 1v3a3 3 0 0 1-3 3H6.164l1.293 1.293a1 1 0 1 1-1.414 1.414l-2.47-2.47a1.75 1.75 0 0 1 0-2.474l2.47-2.47a1 1 0 0 1 1.414 1.414L6.164 17H18a1 1 0 0 0 1-1v-3a1 1 0 0 1 1-1Z"
                    ></path>
                </svg>
            </div>
            <div class="social-button">
                <svg
                    fill="none"
                    width="22"
                    viewBox="0 0 24 24"
                    height="22"
                    style="color: rgb(111, 134, 159); pointer-events: none"
                >
                    <path
                        fill="hsl(211, 20%, 53%)"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M16.734 5.091c-1.238-.276-2.708.047-4.022 1.38a1 1 0 0 1-1.424 0C9.974 5.137 8.504 4.814 7.266 5.09c-1.263.282-2.379 1.206-2.92 2.556C3.33 10.18 4.252 14.84 12 19.348c7.747-4.508 8.67-9.168 7.654-11.7-.541-1.351-1.657-2.275-2.92-2.557Zm4.777 1.812c1.604 4-.494 9.69-9.022 14.47a1 1 0 0 1-.978 0C2.983 16.592.885 10.902 2.49 6.902c.779-1.942 2.414-3.334 4.342-3.764 1.697-.378 3.552.003 5.169 1.286 1.617-1.283 3.472-1.664 5.17-1.286 1.927.43 3.562 1.822 4.34 3.764Z"
                    ></path>
                </svg>
            </div>
            <div class="social-button">
                <svg
                    fill="none"
                    width="22"
                    viewBox="0 0 24 24"
                    height="22"
                    style="color: rgb(111, 134, 159); pointer-events: none"
                >
                    <path
                        fill="hsl(211, 20%, 53%)"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M12.707 3.293a1 1 0 0 0-1.414 0l-4.5 4.5a1 1 0 0 0 1.414 1.414L11 6.414v8.836a1 1 0 1 0 2 0V6.414l2.793 2.793a1 1 0 1 0 1.414-1.414l-4.5-4.5ZM5 12.75a1 1 0 1 0-2 0V20a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-7.25a1 1 0 1 0-2 0V19H5v-6.25Z"
                    ></path>
                </svg>
            </div>
            <div class="social-button">
                <svg
                    fill="none"
                    viewBox="0 0 24 24"
                    width="20"
                    height="20"
                    style="pointer-events: none"
                >
                    <path
                        fill="hsl(211, 20%, 53%)"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M2 12a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm16 0a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm-6-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"
                    ></path>
                </svg>
            </div>
        </div>
    </div>
</template>
