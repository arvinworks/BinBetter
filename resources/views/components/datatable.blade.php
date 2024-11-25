@if($tableId === 'restore-menu-table')
<div  style="margin:-1rem">
@else
<div class="table-card">
@endif

<table id="{{ $tableId }}" class="table text-nowrap table-hover display" style="font-size:12px;">
    <thead class="table-light text-uppercase">
        <tr id="{{ $tableId }}-headers">
            <!-- Headers will be injected here -->
        </tr>
    </thead>
    <tbody id="{{ $tableId }}-body">
        <!-- Table rows will be injected here -->
    </tbody>
</table>
</div>