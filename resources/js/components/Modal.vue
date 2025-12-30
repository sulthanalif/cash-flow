<script setup lang="ts">
import { computed } from 'vue'
import { useMediaQuery } from '@vueuse/core'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import {
  Drawer,
  DrawerClose,
  DrawerContent,
  DrawerDescription,
  DrawerFooter,
  DrawerHeader,
  DrawerTitle,
  DrawerTrigger,
} from '@/components/ui/drawer'

// --- Props & Model ---
const props = defineProps<{
  title?: string
  description?: string
  triggerText?: string
  submitText?: string
  isLoading?: boolean
}>()

// Menggunakan defineModel agar state 'open' bisa dikontrol dari parent jika perlu
const open = defineModel<boolean>('open', { default: false })

// Deteksi Mobile vs Desktop
const isDesktop = useMediaQuery('(min-width: 640px)')

// Mapping Component Dinamis
const Modal = computed(() => ({
  Root: isDesktop.value ? Dialog : Drawer,
  Trigger: isDesktop.value ? DialogTrigger : DrawerTrigger,
  Content: isDesktop.value ? DialogContent : DrawerContent,
  Header: isDesktop.value ? DialogHeader : DrawerHeader,
  Title: isDesktop.value ? DialogTitle : DrawerTitle,
  Description: isDesktop.value ? DialogDescription : DrawerDescription,
  Footer: isDesktop.value ? DialogFooter : DrawerFooter,
  Close: isDesktop.value ? DialogClose : DrawerClose,
}))

// Emit event untuk submit form
const emit = defineEmits(['submit'])
</script>

<template>
  <component :is="Modal.Root" v-model:open="open">

    <!-- <component :is="Modal.Trigger" as-child>
      <slot name="trigger">
        <Button variant="outline">
          {{ triggerText || 'Open' }}
        </Button>
      </slot>
    </component> -->

    <component
      :is="Modal.Content"
      class="sm:max-w-lg"
      :class="[{ 'px-2 pb-8 *:px-4': !isDesktop }]"
    >
      <component :is="Modal.Header" class="text-left">
        <component :is="Modal.Title" v-if="title">
          {{ title }}
        </component>
        <component :is="Modal.Description" v-if="description">
          {{ description }}
        </component>
      </component>

      <div class="py-4 gap-4 grid">
        <slot />
      </div>

      <component :is="Modal.Footer" class="gap-2 pt-2">
        <slot name="footer">
          <component :is="Modal.Close" as-child>
            <Button variant="outline">Cancel</Button>
          </component>

          <Button v-if="submitText"  @click="emit('submit')">
            <Spinner v-if="isLoading" class="animate-spin" />
            {{ submitText }}
          </Button>
        </slot>
      </component>

    </component>
  </component>
</template>
