<script setup lang="ts">
import {
  AlertDialog,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogCancel,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'
import { Loader2 } from 'lucide-vue-next' // Icon loading

// Props yang fleksibel
const props = withDefaults(defineProps<{
  open: boolean              // Mengontrol buka/tutup
  title?: string
  description?: string
  cancelText?: string
  confirmText?: string
  variant?: 'default' | 'destructive' // Bisa 'destructive' agar tombol jadi merah
  isLoading?: boolean
}>(), {
  title: 'Apakah Anda yakin?',
  description: 'Tindakan ini tidak dapat dibatalkan.',
  cancelText: 'Batal',
  confirmText: 'Lanjutkan',
  variant: 'default',
  isLoading: false
})

// Emit untuk komunikasi ke luar
const emit = defineEmits<{
  (e: 'update:open', value: boolean): void
  (e: 'confirm'): void
  (e: 'cancel'): void
}>()

const handleConfirm = () => {
  emit('confirm')
}

const handleCancel = () => {
    emit('cancel')
    emit('update:open', false)
}
</script>

<template>
  <AlertDialog :open="open" @update:open="(val) => emit('update:open', val)">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>{{ title }}</AlertDialogTitle>
        <AlertDialogDescription>
          {{ description }}
        </AlertDialogDescription>
      </AlertDialogHeader>
      
      <AlertDialogFooter>
        <AlertDialogCancel @click="handleCancel" :disabled="isLoading">
            {{ cancelText }}
        </AlertDialogCancel>
        
        <Button 
            :variant="variant" 
            @click="handleConfirm" 
            :disabled="isLoading"
        >
            <Loader2 v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
            {{ confirmText }}
        </Button>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>