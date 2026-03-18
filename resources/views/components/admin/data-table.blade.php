@props([
    'headers' => [],
    'rows' => [],
    'emptyTitle' => 'No data found',
    'emptyDescription' => 'There is no data to display at the moment.',
    'emptyIcon' => 'database_off',
    'emptyActionHref' => null,
    'emptyActionLabel' => null
])

<div class="glass-card rounded-[3rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
    @if(count($rows) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                @if(count($headers) > 0)
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            @foreach($headers as $header)
                                <th class="px-10 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 dark:border-slate-800">
                                    {{ $header }}
                                </th>
                            @endforeach
                            @if(isset($actions))
                                <th class="px-10 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 dark:border-slate-800 text-right">
                                    Actions
                                </th>
                            @endif
                        </tr>
                    </thead>
                @endif
                <tbody>
                    @foreach($rows as $index => $row)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-50 dark:border-slate-800/50 last:border-0">
                            @foreach($row as $cell)
                                <td class="px-10 py-6">
                                    {!! $cell !!}
                                </td>
                            @endforeach
                            @if(isset($actions))
                                <td class="px-10 py-6 text-right">
                                    {{ $actions($row, $index) }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-admin.empty-state 
            :title="$emptyTitle" 
            :description="$emptyDescription" 
            :icon="$emptyIcon"
            :actionHref="$emptyActionHref"
            :actionLabel="$emptyActionLabel"
        />
    @endif
</div>
