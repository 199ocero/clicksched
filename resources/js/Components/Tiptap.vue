<script setup>
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/shadcn/ui/tooltip';
import CharacterCount from '@tiptap/extension-character-count';
import HardBreak from '@tiptap/extension-hard-break';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { LetterText, WholeWord } from 'lucide-vue-next';
import { computed, watch } from 'vue';
import BlueskyLogo from './Platforms/Logos/BlueskyLogo.vue';

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Start typing...',
    },
    modelValue: {
        type: [Object, null],
        default: null,
    },
    selectedPlatforms: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    content: props.modelValue || {
        type: 'doc',
        content: [{ type: 'paragraph' }],
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getJSON());
    },
    extensions: [
        StarterKit.configure({
            hardBreak: false,
        }),
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
        Link.configure({
            autolink: false,
            openOnClick: false,
            defaultProtocol: 'https',
            linkOnPaste: true,
        }),
        CharacterCount,
        HardBreak.extend({
            addKeyboardShortcuts() {
                return {
                    Enter: () => {
                        if (
                            this.editor.isActive('orderedList') ||
                            this.editor.isActive('bulletList')
                        ) {
                            return this.editor
                                .chain()
                                .createParagraphNear()
                                .run();
                        }
                        return this.editor.commands.setHardBreak();
                    },
                };
            },
        }),
    ],
    autofocus: true,
    editorProps: {
        attributes: {
            class: 'prose prose-sm dark:prose-invert prose-p:my-1 resize-y !max-w-none p-3 min-h-[12rem] max-h-[20rem] overflow-y-auto overflow-x-auto border border-zinc-200 dark:border-zinc-800 rounded-md w-full break-all',
        },
    },
});

watch(
    () => props.modelValue,
    (newValue) => {
        if (editor.value && newValue) {
            const currentContent = editor.value.getJSON();
            if (JSON.stringify(currentContent) !== JSON.stringify(newValue)) {
                editor.value.commands.setContent(newValue, true);
            }
        }
    },
);

const clearContent = () => {
    if (editor.value) {
        editor.value.commands.clearContent();
        emit('update:modelValue', {
            type: 'doc',
            content: [{ type: 'paragraph' }],
        });
    }
};

const getHtml = () => {
    if (editor.value) {
        return editor.value.getHTML();
    }
    return '';
};

const characterCount = computed(
    () => editor.value?.storage.characterCount?.characters() || 0,
);

const wordCount = computed(
    () => editor.value?.storage.characterCount?.words() || 0,
);

const blueskyRemainingCharacters = computed(() => 300 - characterCount.value);

defineExpose({
    clearContent,
    getHtml,
});
</script>

<template>
    <template v-if="selectedPlatforms.length > 0 && editor">
        <div class="mb-4 flex flex-wrap gap-1">
            <template v-for="platform in selectedPlatforms">
                <div v-if="platform === 'bluesky'">
                    <TooltipProvider :delay-duration="0">
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <div
                                    v-if="platform === 'bluesky'"
                                    class="relative inline-block cursor-pointer"
                                >
                                    <div
                                        class="absolute -top-4 left-1/2 flex h-6 w-6 -translate-x-1/2 items-center justify-center rounded-full border bg-white p-1 dark:bg-zinc-900"
                                        :class="{
                                            'border-green-500 text-green-600':
                                                blueskyRemainingCharacters >= 0,
                                            'border-red-500 text-red-600':
                                                blueskyRemainingCharacters < 0,
                                        }"
                                    >
                                        <div
                                            class="flex items-center justify-center p-[2px]"
                                        >
                                            <BlueskyLogo width="3" height="3" />
                                        </div>
                                    </div>

                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full border"
                                        :class="{
                                            'border-green-500 text-green-600':
                                                blueskyRemainingCharacters >= 0,
                                            'border-red-500 text-red-600':
                                                blueskyRemainingCharacters < 0,
                                        }"
                                    >
                                        <span class="text-xs">
                                            {{ blueskyRemainingCharacters }}
                                        </span>
                                    </div>
                                </div>
                            </TooltipTrigger>
                            <TooltipContent>
                                <span>
                                    Your Bluesky post can be up to 300
                                    characters long. Any text beyond this limit
                                    will be cut off.
                                </span>
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                </div>
            </template>
        </div>
    </template>

    <editor-content :editor="editor" />

    <div
        v-if="editor"
        class="mt-2 flex items-center justify-between px-1 text-xs text-zinc-500 dark:text-zinc-400"
    >
        <span class="flex items-center space-x-1">
            <LetterText class="h-4 w-4" />
            <span class="text-zinc-900 dark:text-white"
                >{{ characterCount }} characters</span
            >
        </span>
        <span class="flex items-center space-x-1">
            <WholeWord class="h-4 w-4" />
            <span class="text-zinc-900 dark:text-white"
                >{{ wordCount }} words</span
            >
        </span>
    </div>
</template>
