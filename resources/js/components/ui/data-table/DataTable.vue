<script setup lang="ts" generic="TData, TValue">
import type {
    ColumnDef,
    ColumnFiltersState,
    SortingState,
    VisibilityState,
} from '@tanstack/vue-table'
import {
    FlexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table'
import { ref, computed, h, type Component } from 'vue'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Checkbox } from '@/components/ui/checkbox'
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
    DropdownMenuSeparator
} from '@/components/ui/dropdown-menu'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination'
// Import Icon Trash untuk tombol delete
import { ChevronDown, MoreHorizontal, Trash2 } from 'lucide-vue-next'
import { valueUpdater } from '@/lib/utils'

export interface ActionItem<T> {
    label: string
    icon?: Component
    class?: string
    onClick: (row: T) => void
}

const props = defineProps<{
    columns: ColumnDef<TData, TValue>[]
    data: TData[]
    actions?: boolean
    actionList?: ActionItem<TData>[]
    rowSelection?: boolean // Prop untuk mengaktifkan fitur select
}>()

// --- 1. DEFINISIKAN EMIT BARU ---
// Agar parent tau ada request hapus data banyak
const emit = defineEmits(['delete-selected'])

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})

const tableColumns = computed(() => {
    const cols = [...props.columns]

    // --- 2. PERBAIKAN LOGIKA: Gunakan props.rowSelection ---
    if (props.rowSelection) {
        cols.unshift({
            id: 'select',
            header: ({ table }) => h(Checkbox, {
                'modelValue': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
                'onUpdate:modelValue': value => table.toggleAllPageRowsSelected(!!value),
                'ariaLabel': 'Select all',
            }),
            cell: ({ row }) => h(Checkbox, {
                'modelValue': row.getIsSelected(),
                'onUpdate:modelValue': value => row.toggleSelected(!!value),
                'ariaLabel': 'Select row',
            }),
            enableSorting: false,
            enableHiding: false,
        })
    }

    if (props.actions && props.actionList) {
        cols.push({
            id: 'actions',
            enableHiding: false,
            cell: ({ row }) => {
                return h(DropdownMenu, {}, {
                    default: () => [
                        h(DropdownMenuTrigger, { asChild: true }, () =>
                            h(Button, { variant: 'ghost', class: 'h-8 w-8 p-0' }, () =>
                                [h('span', { class: 'sr-only' }, 'Open menu'), h(MoreHorizontal, { class: 'h-4 w-4' })]
                            )
                        ),
                        h(DropdownMenuContent, { align: 'end' }, () => [
                            h(DropdownMenuLabel, () => 'Actions'),
                            h(DropdownMenuSeparator),
                            ...props.actionList!.map((action) =>
                                h(DropdownMenuItem, {
                                    class: action.class,
                                    onClick: () => action.onClick(row.original)
                                }, () => [
                                    action.icon ? h(action.icon, { class: 'mr-2 h-4 w-4' }) : null,
                                    action.label
                                ])
                            )
                        ])
                    ]
                })
            }
        })
    }
    return cols
})

const table = useVueTable({
    get data() { return props.data },
    get columns() { return tableColumns.value },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
    state: {
        get sorting() { return sorting.value },
        get columnFilters() { return columnFilters.value },
        get columnVisibility() { return columnVisibility.value },
        get rowSelection() { return rowSelection.value },
    },
})

// --- 3. FUNGSI HANDLE DELETE ---
const handleDeleteSelected = () => {
    // Ambil data original dari row yang dipilih
    const selectedRows = table.getFilteredSelectedRowModel().rows.map(row => row.original)
    // Kirim ke parent
    emit('delete-selected', selectedRows)
    // Reset selection setelah delete (opsional, tergantung UX)
    rowSelection.value = {}
}
</script>

<template>
    <div class="w-full">
        <div class="flex items-center py-4 gap-2">
            <slot name="toolbar" :table="table">
                <Input class="max-w-sm" placeholder="Search..." :model-value="table.getState().globalFilter"
                    @update:model-value="table.setGlobalFilter($event)" />
            </slot>

            <Button
                v-if="Object.keys(rowSelection).length > 0"
                variant="destructive"
                size="sm"
                class="ml-auto mr-2"
                @click="handleDeleteSelected"
            >
                <Trash2 class="mr-2 h-4 w-4" />
                Delete ({{ Object.keys(rowSelection).length }})
            </Button>

            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" :class="Object.keys(rowSelection).length > 0 ? '' : 'ml-auto'">
                        Columns
                        <ChevronDown class="ml-2 h-4 w-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuCheckboxItem
                        v-for="column in table.getAllColumns().filter((column) => column.getCanHide())" :key="column.id"
                        class="capitalize" :model-value="column.getIsVisible()" @update:model-value="(value) => {
                            column.toggleVisibility(!!value)
                        }">
                        {{ column.id }}
                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
                            :data-state="row.getIsSelected() && 'selected'">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableRow v-else>
                        <TableCell :colspan="tableColumns.length" class="h-24 text-center">
                            No results.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <div class="flex items-center justify-end space-x-2 py-4">
            <div class="flex-1 text-sm text-muted-foreground">
                Page {{ table.getState().pagination.pageIndex + 1 }} of
                {{ table.getPageCount() }} ·
                {{ table.getFilteredRowModel().rows.length }} data listed
            </div>

            <div class="flex flex-col gap-6">
                <Pagination v-slot="{ page }" :total="table.getFilteredRowModel().rows.length"
                    :items-per-page="table.getState().pagination.pageSize"
                    :page="table.getState().pagination.pageIndex + 1" :sibling-count="1" show-edges
                    @update:page="(val) => table.setPageIndex(val - 1)">
                    <PaginationContent v-slot="{ items }">
                        <PaginationPrevious :disabled="!table.getCanPreviousPage()" />
                        <template v-for="(item, index) in items">
                            <PaginationItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                <Button size="sm" :variant="item.value === page ? 'default' : 'outline'"
                                    @click="table.setPageIndex(item.value - 1)">
                                    {{ item.value }}
                                </Button>
                            </PaginationItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>
                        <PaginationNext :disabled="!table.getCanNextPage()" />
                    </PaginationContent>
                </Pagination>
            </div>
        </div>
    </div>
</template>
