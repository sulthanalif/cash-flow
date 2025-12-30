<script setup lang="ts">
import { ref } from 'vue'
import { CheckIcon, ChevronsUpDownIcon } from 'lucide-vue-next'
import { cn } from '@/lib/utils' // Pastikan path ini benar
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'

// Mendifinisikan tipe data untuk opsi
interface Option {
  value: string
  label: string
}

// 1. Menerima Data dari Luar (Props)
const props = defineProps<{
  options: Option[]            // Data array (frameworks, users, dll)
  modelValue: string           // Nilai yang dipilih (v-model)
  placeholder?: string         // Teks placeholder (opsional)
  searchPlaceholder?: string   // Teks pencarian (opsional)
  emptyText?: string           // Teks jika data tidak ada (opsional)
}>()

// 2. Mengirim Event ke Luar (Emits)
const emit = defineEmits(['update:modelValue'])

const open = ref(false)

// Fungsi untuk menangani seleksi
function handleSelect(currentValue: string) {
  // Jika diklik lagi, hapus seleksi (toggle), atau set nilai baru
  const newValue = currentValue === props.modelValue ? '' : currentValue
  
  // Kirim nilai baru ke parent
  emit('update:modelValue', newValue) 
  open.value = false
}
</script>

<template>
  <Popover v-model:open="open">
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        role="combobox"
        :aria-expanded="open"
        class="w-[200px] justify-between"
      >
        {{
          modelValue
            ? options.find((opt) => opt.value === modelValue)?.label
            : placeholder || 'Select item...'
        }}
        <ChevronsUpDownIcon class="ml-2 h-4 w-4 shrink-0 opacity-50" />
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-[200px] p-0">
      <Command>
        <CommandInput :placeholder="searchPlaceholder || 'Search...'" />
        <CommandList>
            <CommandEmpty>{{ emptyText || 'No item found.' }}</CommandEmpty>
            <CommandGroup>
            <CommandItem
                v-for="option in options"
                :key="option.value"
                :value="option.value"
                @select="() => handleSelect(option.value)"
            >
                <CheckIcon
                :class="cn(
                    'mr-2 h-4 w-4',
                    modelValue === option.value ? 'opacity-100' : 'opacity-0'
                )"
                />
                {{ option.label }}
            </CommandItem>
            </CommandGroup>
        </CommandList>
      </Command>
    </PopoverContent>
  </Popover>
</template>