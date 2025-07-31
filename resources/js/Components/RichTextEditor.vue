<template>
    <div class="rich-text-editor" dir="rtl">
        <!-- Toolbar -->
        <div class="editor-toolbar bg-gray-50 border border-gray-200 rounded-t-lg p-2 flex flex-wrap items-center gap-1">
            <!-- Text Formatting -->
            <div class="flex items-center gap-1 border-l border-gray-300 pl-2 ml-2">
                <button
                type="button"
                @click="editor.chain().focus().toggleBold().run()"
                :class="{ 'bg-blue-100 text-blue-600': editor?.isActive('bold') }"
                class="p-2 rounded hover:bg-gray-100 transition-colors"
                title="نص عريض"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3h5.5c1.5 0 2.5 1 2.5 2.5S12 8 10.5 8H5V3zm0 7h6c1.5 0 3 1 3 2.5S12.5 15 11 15H5v-5z"/>
                    </svg>
                </button>
                
                <button
                type="button"
                @click="editor.chain().focus().toggleItalic().run()"
                :class="{ 'bg-blue-100 text-blue-600': editor?.isActive('italic') }"
                class="p-2 rounded hover:bg-gray-100 transition-colors"
                title="نص مائل"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 3h6v2H12.5l-3 10H12v2H6v-2h1.5l3-10H8V3z"/>
                    </svg>
                </button>
            </div>

            <!-- Text Alignment -->
            <div class="flex items-center gap-1 border-l border-gray-300 pl-2 ml-2">
                <button
                type="button"
                @click="editor.chain().focus().setTextAlign('right').run()"
                :class="{ 'bg-blue-100 text-blue-600': editor?.isActive({ textAlign: 'right' }) }"
                class="p-2 rounded hover:bg-gray-100 transition-colors"
                title="محاذاة يمين"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 4h16v2H2V4zm4 4h12v2H6V8zm-4 4h16v2H2v-2zm4 4h12v2H6v-2z"/>
                    </svg>
                </button>
                
                <button
                type="button"
                @click="editor.chain().focus().setTextAlign('center').run()"
                :class="{ 'bg-blue-100 text-blue-600': editor?.isActive({ textAlign: 'center' }) }"
                class="p-2 rounded hover:bg-gray-100 transition-colors"
                title="محاذاة وسط"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 4h16v2H2V4zm2 4h12v2H4V8zm-2 4h16v2H2v-2zm2 4h12v2H4v-2z"/>
                    </svg>
                </button>
                
                <button
                type="button"
                @click="editor.chain().focus().setTextAlign('left').run()"
                :class="{ 'bg-blue-100 text-blue-600': editor?.isActive({ textAlign: 'left' }) }"
                class="p-2 rounded hover:bg-gray-100 transition-colors"
                title="محاذاة يسار"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 4h16v2H2V4zm0 4h12v2H2V8zm0 4h16v2H2v-2zm0 4h12v2H2v-2z"/>
                    </svg>
                </button>
            </div>

            <!-- Lists -->
            <div class="flex items-center gap-1 border-l border-gray-300 pl-2 ml-2">
                <button
                type="button"
                @click="editor.chain().focus().toggleBulletList().run()"
                :class="{ 'bg-blue-100 text-blue-600': editor?.isActive('bulletList') }"
                class="p-2 rounded hover:bg-gray-100 transition-colors"
                title="قائمة نقطية"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 6a2 2 0 110-4 2 2 0 010 4zM4 13a2 2 0 110-4 2 2 0 010 4zM4 20a2 2 0 110-4 2 2 0 010 4zM8 5h10v2H8V5zm0 7h10v2H8v-2zm0 7h10v2H8v-2z"/>
                    </svg>
                </button>
                
                <button
                type="button"
                @click="editor.chain().focus().toggleOrderedList().run()"
                :class="{ 'bg-blue-100 text-blue-600': editor?.isActive('orderedList') }"
                class="p-2 rounded hover:bg-gray-100 transition-colors"
                title="قائمة مرقمة"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4h2v12H3V4zm2 0h12v2H5V4zm0 4h12v2H5V8zm0 4h12v2H5v-2z"/>
                    </svg>
                </button>
            </div>

            <!-- Headings -->
            <div class="flex items-center gap-1">
                <select 
                    @change="setHeading($event.target.value)"
                    class="text-sm border border-gray-300 rounded px-2 py-1"
                    :value="getActiveHeading()"
                >
                    <option value="">فقرة عادية</option>
                    <option value="1">عنوان 1</option>
                    <option value="2">عنوان 2</option>
                    <option value="3">عنوان 3</option>
                    <option value="4">عنوان 4</option>
                </select>
            </div>
        </div>

        <!-- Editor Content -->
        <div class="editor-content">
            <editor-content 
                :editor="editor" 
                class="prose prose-lg max-w-none min-h-[300px] p-4 border border-t-0 border-gray-200 rounded-b-lg focus-within:border-blue-500 bg-white"
                style="direction: rtl; text-align: right;"
            />
        </div>

        <!-- Character count -->
        <div class="text-sm text-gray-500 mt-2 text-left">
            {{ editor?.storage.characterCount.characters() || 0 }} حرف
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Editor, EditorContent } from '@tiptap/vue-3'
import { StarterKit } from '@tiptap/starter-kit'
import { TextAlign } from '@tiptap/extension-text-align'
import { TextStyle } from '@tiptap/extension-text-style'
import { CharacterCount } from '@tiptap/extension-character-count'

// Props
const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    initialContent: {
        type: String,
        default: null // Changed to null to properly detect when it's provided
    },
    placeholder: {
        type: String,
        default: 'اكتب النص هنا...'
    }
})

// Emits
const emit = defineEmits(['update:modelValue'])

// Editor instance
const editor = ref(null)

// Determine if we're in initialContent mode (read-only mode without auto-emit)
const isInitialContentMode = props.initialContent !== null

// Internal content state - use initialContent if provided, otherwise modelValue
const internalContent = ref(isInitialContentMode ? props.initialContent : props.modelValue)

// Timeout for debounced updates
const updateTimeout = ref(null)

// Create editor
const createEditor = () => {
    const initialEditorContent = isInitialContentMode ? props.initialContent : props.modelValue
    
    editor.value = new Editor({
        content: initialEditorContent,
        editorProps: {
            attributes: {
                dir: 'rtl',
                class: 'prose prose-lg max-w-none focus:outline-none',
                style: 'direction: rtl; text-align: right; line-height: 1.8;'
            }
        },
        extensions: [
            StarterKit.configure({
                heading: {
                    levels: [1, 2, 3, 4]
                }
            }),
            TextAlign.configure({
                types: ['heading', 'paragraph'],
                alignments: ['left', 'center', 'right'],
                defaultAlignment: 'right'
            }),
            TextStyle,
            CharacterCount
        ],
        onUpdate: ({ editor }) => {
            // Always store content internally for getCurrentContent()
            internalContent.value = editor.getHTML()
            
            // NEVER auto-emit when in initialContent mode - only emit when using v-model
            if (!isInitialContentMode) {
                clearTimeout(updateTimeout.value)
                updateTimeout.value = setTimeout(() => {
                    emit('update:modelValue', internalContent.value)
                }, 500)
            }
        },
        onCreate: ({ editor }) => {
            // Set RTL direction on creation
            editor.commands.setTextAlign('right')
        }
    })
}

// Method to get current content (for parent components to access)
const getCurrentContent = () => {
    return internalContent.value
}

// Method to force immediate update (for form submission when using v-model)
const forceUpdate = () => {
    if (!isInitialContentMode && updateTimeout.value) {
        clearTimeout(updateTimeout.value)
        emit('update:modelValue', internalContent.value)
    }
}

// Expose methods to parent
defineExpose({
    getCurrentContent,
    forceUpdate
})

// Helper methods
const setHeading = (level) => {
    if (level === '') {
        editor.value.chain().focus().setParagraph().run()
    } else {
        editor.value.chain().focus().toggleHeading({ level: parseInt(level) }).run()
    }
}

const getActiveHeading = () => {
    for (let level = 1; level <= 4; level++) {
        if (editor.value?.isActive('heading', { level })) {
            return level.toString()
        }
    }
    return ''
}

// Watch for external changes (only when using v-model)
watch(() => props.modelValue, (newValue) => {
    if (editor.value && editor.value.getHTML() !== newValue && !isInitialContentMode) {
        editor.value.commands.setContent(newValue, false)
        internalContent.value = newValue
    }
})

// Watch for initialContent changes (only when using initialContent prop)
watch(() => props.initialContent, (newValue) => {
    if (editor.value && editor.value.getHTML() !== newValue && isInitialContentMode) {
        editor.value.commands.setContent(newValue, false)
        internalContent.value = newValue
    }
})

// Lifecycle
onMounted(() => {
    createEditor()
})

onBeforeUnmount(() => {
    if (updateTimeout.value) {
        clearTimeout(updateTimeout.value)
    }
    if (editor.value) {
        editor.value.destroy()
    }
})
</script>

<style scoped>
.rich-text-editor {
    font-family: 'Cairo', 'Noto Sans Arabic', sans-serif;
}

.editor-content :deep(.ProseMirror) {
    direction: rtl;
    text-align: right;
    outline: none;
    line-height: 1.8;
}

.editor-content :deep(.ProseMirror p) {
    margin: 0.5rem 0;
    text-align: right;
}

.editor-content :deep(.ProseMirror h1),
.editor-content :deep(.ProseMirror h2),
.editor-content :deep(.ProseMirror h3),
.editor-content :deep(.ProseMirror h4) {
    margin: 1rem 0 0.5rem 0;
    font-weight: bold;
    text-align: right;
}

.editor-content :deep(.ProseMirror h1) {
    font-size: 2rem;
}

.editor-content :deep(.ProseMirror h2) {
    font-size: 1.5rem;
}

.editor-content :deep(.ProseMirror h3) {
    font-size: 1.25rem;
}

.editor-content :deep(.ProseMirror h4) {
    font-size: 1.125rem;
}

.editor-content :deep(.ProseMirror ul),
.editor-content :deep(.ProseMirror ol) {
    margin: 0.5rem 0;
    padding-right: 1.5rem;
    padding-left: 0;
}

.editor-content :deep(.ProseMirror li) {
    margin: 0.25rem 0;
}

.editor-content :deep(.ProseMirror strong),
.editor-content :deep(.ProseMirror b) {
    font-weight: bold;
}

.editor-content :deep(.ProseMirror em),
.editor-content :deep(.ProseMirror i) {
    font-style: italic;
}

/* Center alignment */
.editor-content :deep(.ProseMirror [style*="text-align: center"]) {
    text-align: center !important;
}

/* Left alignment */
.editor-content :deep(.ProseMirror [style*="text-align: left"]) {
    text-align: left !important;
}

/* Right alignment (default for RTL) */
.editor-content :deep(.ProseMirror [style*="text-align: right"]) {
    text-align: right !important;
}
</style>
