<script setup lang="ts">
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { cn } from '@/lib/utils'

// Definisi Kolom
export interface Column {
  key: string
  label: string
  class?: string // Untuk styling header (misal: text-right)
  cellClass?: string // Untuk styling isi cell (misal: font-bold)
}

const props = defineProps<{
  columns: Column[]
  data: any[]
  caption?: string
  emptyMessage?: string
}>()
</script>

<template>
  <div class="rounded-md border">
    <Table>
      <TableCaption v-if="caption">{{ caption }}</TableCaption>

      <TableHeader>
        <TableRow>
          <TableHead
            v-for="col in columns"
            :key="col.key"
            :class="col.class"
          >
            {{ col.label }}
          </TableHead>
        </TableRow>
      </TableHeader>

      <TableBody>
        <template v-if="data.length > 0">
          <TableRow v-for="(row, index) in data" :key="index">

            <TableCell
              v-for="col in columns"
              :key="col.key"
              :class="col.cellClass"
            >
              <slot :name="col.key" :row="row" :value="row[col.key]">
                {{ row[col.key] }}
              </slot>
            </TableCell>

          </TableRow>
        </template>

        <template v-else>
          <TableRow>
            <TableCell :colspan="columns.length" class="h-24 text-center">
              {{ emptyMessage || 'No results.' }}
            </TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
  </div>
</template>
