<script setup lang="ts">
import { type Component } from 'vue'
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { cn } from '@/lib/utils'

interface Props {
  title: string
  value: string | number
  icon?: Component
  description?: string
  // Opsional: Untuk memberi warna pada deskripsi (misal: naik=hijau, turun=merah)
  trend?: 'up' | 'down' | 'neutral'
}

const props = withDefaults(defineProps<Props>(), {
  trend: 'neutral'
})
</script>

<template>
  <Card>
    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
      <CardTitle class="text-sm font-medium">
        {{ title }}
      </CardTitle>

      <component
        :is="icon"
        v-if="icon"
        class="h-4 w-4 text-muted-foreground"
      />
    </CardHeader>

    <CardContent>
      <div class="text-2xl font-bold">
        {{ value }}
      </div>

      <p
        v-if="description"
        class="text-xs mt-1"
        :class="cn(
          'text-muted-foreground', // Default abu-abu
          trend === 'up' && 'text-green-600 font-medium', // Hijau jika naik
          trend === 'down' && 'text-red-600 font-medium'  // Merah jika turun
        )"
      >
        {{ description }}
      </p>
    </CardContent>
  </Card>
</template>
