<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Check, ChevronDown, X } from 'lucide-vue-next'
import {
  ListboxContent,
  ListboxFilter,
  ListboxItem,
  ListboxItemIndicator,
  ListboxRoot,
  useFilter
} from 'reka-ui'
import { Button } from '@/components/ui/button'
import { Popover, PopoverAnchor, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import {
  TagsInput,
  TagsInputInput,
  TagsInputItem,
  TagsInputItemDelete,
  TagsInputItemText
} from '@/components/ui/tags-input'

// --- DEFINISI TIPE & PROPS ---
interface Option {
  value: string
  label: string
}

const props = withDefaults(defineProps<{
  options: Option[]            // Data Array [{value: '1', label: 'Admin'}, ...]
  modelValue?: string[]        // Data yang terpilih (v-model)
  placeholder?: string
  emptyText?: string
}>(), {
  modelValue: () => [],
  placeholder: 'Select items...',
  emptyText: 'No results found.'
})

const emit = defineEmits(['update:modelValue'])

// --- STATE & LOGIC ---
const open = ref(false)
const searchTerm = ref('')

// Proxy v-model agar sinkron dengan Parent
const selectedValues = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const { contains } = useFilter({ sensitivity: 'base' })

// Filter opsi berdasarkan pencarian
const filteredOptions = computed(() =>
  searchTerm.value === ''
    ? props.options
    : props.options.filter(option => contains(option.label, searchTerm.value))
)

// Buka dropdown saat mengetik
watch(searchTerm, (val) => {
  if (val) open.value = true
})
</script>

<template>
  <Popover v-model:open="open">
    <ListboxRoot
      v-model="selectedValues"
      class="w-full"
      multiple
    >
      <PopoverAnchor class="block w-full">
        <TagsInput v-slot="{ modelValue: tags }" v-model="selectedValues" class="flex flex-wrap gap-1 w-full border rounded-md px-3 py-2 bg-background min-h-[40px]">
          
          <TagsInputItem 
            v-for="itemValue in tags" 
            :key="itemValue.toString()" 
            :value="itemValue"
            class="flex items-center gap-1 bg-secondary text-secondary-foreground px-1.5 py-0.5 rounded text-sm"
          >
            <TagsInputItemText>
                {{ options.find(opt => opt.value === itemValue)?.label || itemValue }}
            </TagsInputItemText>
            <TagsInputItemDelete class="cursor-pointer p-0.5 hover:bg-destructive/20 rounded">
                <X class="w-3 h-3" />
            </TagsInputItemDelete>
          </TagsInputItem>

          <ListboxFilter v-model="searchTerm" as-child>
            <TagsInputInput 
              :placeholder="tags.length === 0 ? placeholder : ''" 
              class="flex-1 bg-transparent outline-none text-sm min-w-[100px]"
              @keydown.enter.prevent
              @keydown.down="open = true"
            />
          </ListboxFilter>

          <PopoverTrigger as-child>
            <Button variant="ghost" size="sm" class="ml-auto h-6 w-6 p-0 opacity-50 hover:opacity-100">
              <ChevronDown class="h-4 w-4" />
            </Button>
          </PopoverTrigger>
        </TagsInput>
      </PopoverAnchor>

      <PopoverContent 
        class="w-[--reka-popper-anchor-width] p-0" 
        align="start"
        @open-auto-focus.prevent
      >
        <ListboxContent class="max-h-[200px] overflow-y-auto p-1">
            
          <div v-if="filteredOptions.length === 0" class="py-6 text-center text-sm text-muted-foreground">
            {{ emptyText }}
          </div>

          <ListboxItem
            v-for="option in filteredOptions"
            :key="option.value"
            :value="option.value"
            class="relative flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none data-[highlighted]:bg-accent data-[highlighted]:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
            @select="searchTerm = ''"
          >
            <div class="flex items-center gap-2 w-full">
                <span>{{ option.label }}</span>
                <ListboxItemIndicator class="ml-auto">
                    <Check class="h-4 w-4" />
                </ListboxItemIndicator>
            </div>
          </ListboxItem>
        </ListboxContent>
      </PopoverContent>
    </ListboxRoot>
  </Popover>
</template>