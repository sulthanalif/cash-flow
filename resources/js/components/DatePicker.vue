<script setup lang="ts">
import { computed, ref } from 'vue' // <--- Tambahkan ref
import {
  DateFormatter,
  type DateValue,
  getLocalTimeZone,
  parseDate
} from '@internationalized/date'
import { CalendarIcon } from 'lucide-vue-next'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'

const props = defineProps<{
  modelValue?: string | DateValue | undefined
  placeholder?: string
  class?: string
}>()

const emit = defineEmits(['update:modelValue'])

const df = new DateFormatter('id-ID', {
  dateStyle: 'long',
})

// 1. STATE UNTUK KONTROL POPOVER
const isPopoverOpen = ref(false)

const date = computed({
  get: () => {
    if (!props.modelValue) return undefined
    if (typeof props.modelValue === 'string') {
        try { return parseDate(props.modelValue) } catch { return undefined }
    }
    return props.modelValue
  },
  set: (val) => emit('update:modelValue', val)
})
</script>

<template>
  <Popover v-model:open="isPopoverOpen">
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        :class="cn(
          'w-full justify-start text-left font-normal',
          !date && 'text-muted-foreground',
          props.class
        )"
      >
        <CalendarIcon class="mr-2 h-4 w-4" />
        {{ date ? df.format(date.toDate(getLocalTimeZone())) : (props.placeholder || "Pick a date") }}
      </Button>
    </PopoverTrigger>

    <PopoverContent class="w-auto p-0">
      <Calendar
        v-model="date"
        initial-focus
        @update:model-value="isPopoverOpen = false"
      />
      </PopoverContent>
  </Popover>
</template>
